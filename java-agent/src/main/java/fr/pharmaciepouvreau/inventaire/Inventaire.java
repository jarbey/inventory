package fr.pharmaciepouvreau.inventaire;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URI;
import java.net.URISyntaxException;
import java.util.List;

import com.google.gson.Gson;

import fr.pharmaciepouvreau.inventaire.dao.DAOFactory;
import fr.pharmaciepouvreau.inventaire.dto.Message;
import fr.pharmaciepouvreau.inventaire.dto.Produit;

public class Inventaire {
	private static final String UPDATE_STOCK = "update_stock";
	private static final String SEARCH_ACTION = "search";
	private static final String INVENTORY_SCAN = "inventory_scan";

	private static final String READY_STATE = "ready";

	private static WebSocketClientEndpoint clientEndPoint = null;

	private static final Gson GSON = new Gson();

	private static void connectWebSocketServer() {
		try {
			// open websocket
			clientEndPoint = new WebSocketClientEndpoint(new URI("ws://inventory.pharmacie-pouvreau.fr/ws"));

			// add listener
			clientEndPoint.addMessageHandler(new WebSocketClientEndpoint.MessageHandler() {
				public void handleMessage(String content) {
					// Console out the message
					System.out.println("Message received : " + content);

					Message<Produit> message = (Message<Produit>) GSON.fromJson(content, Message.class);
					if (message.getAction() != null) {
						switch (message.getAction()) {
						case UPDATE_STOCK:
							Produit product = ((Produit) message.getResult());
							System.out.println("Update stock for " + product.getId() + " => " + product.getStock());
							DAOFactory.getLGPIDao().updateStockProduit(product);
							break;
						case SEARCH_ACTION:
							List<Produit> results = DAOFactory.getLGPIDao().searchProduit(message.getDescription());
							System.out.println("Search for " + message.getDescription() + " => " + results.size() + " results");

							message.setResults(results);
							message.setResult(null);

							clientEndPoint.sendMessage(GSON.toJson(message));
							break;
						default:
							System.out.println("Unknown action " + message.getAction());
							break;
						}
					} else {
						System.out.println("Unknown action " + message.getAction());
					}
				}
			});

		} catch (URISyntaxException ex) {
			System.err.println("URISyntaxException exception: " + ex.getMessage());
		}
	}

	/**
	 * @param args
	 * @throws IOException
	 */
	public static void main(String[] args) throws IOException {
		connectWebSocketServer();
		
		// Connect to oracle datasource
		DAOFactory.getLGPIDao().getProduit("666");

		BufferedReader reader = new BufferedReader(new InputStreamReader(System.in));
		Produit produit = null;

		clientEndPoint.sendMessage(getStateMessage(READY_STATE));

		String data = "";
		while (true) {
			data = reader.readLine();
			produit = DAOFactory.getLGPIDao().getProduit(getCode(data));
			if (produit != null) {
				// send message to websocket
				if (clientEndPoint.getUserSession() == null) {
					connectWebSocketServer();
				}

				Message<Produit> message = new Message<Produit>();
				message.setAction(INVENTORY_SCAN);
				message.setResult(produit);
				clientEndPoint.sendMessage(GSON.toJson(message));
				//

				// Output console
				System.out.println(produit.toString());
			}
		}
	}

	private static String getCode(String code) {
		if (code.length() == 7) {
			// Code CIP on le garde tel quel
			return code;
		} else if (code.length() == 13) {
			// Code EAN on le garde tel quel
			return code;
		} else if (code.length() == 16) {
			// Il faut prendre le code cip après le premier chiffre
			return code.substring(1, 8);
		} else if (code.length() > 16) {
			// Certainement un data matrix, on prend les 13 caractères après le troisième pour avoir le code ean
			return code.substring(3, 16);
		}

		// Sinon ??? On revoit le code par defaut
		return code;
	}

	private static String getStateMessage(String state) {
		return "{\"action\":\"" + state + "\"}";
	}

}