package fr.pharmaciepouvreau.inventaire;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.lang.reflect.Type;
import java.net.URI;
import java.net.URISyntaxException;
import java.util.List;

import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import fr.pharmaciepouvreau.inventaire.dao.DAOFactory;
import fr.pharmaciepouvreau.inventaire.dto.Message;
import fr.pharmaciepouvreau.inventaire.dto.Produit;

public class Inventaire {
	private static final String UPDATE_STOCK = "update_stock";
	private static final String SEARCH_ACTION = "search";
	private static final String INVENTORY_SCAN = "inventory_scan";
	private static final String MANUAL_SCAN = "manual_scan";

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

					Type fooType = new TypeToken<Message<Produit>>() {}.getType();
					Message<Produit> message = GSON.fromJson(content, fooType);
					if (message.getAction() != null) {
						switch (message.getAction()) {
						case UPDATE_STOCK:
							Produit product = ((Produit) message.getResult());
							System.out.println("Update stock for " + product.getId() + " => " + product.getStock());
							DAOFactory.getLGPIDao().updateStockProduit(product);
							break;
						case MANUAL_SCAN:
							System.out.println("Manual scan : " + message.getDescription());
							Produit produit = DAOFactory.getLGPIDao().getProduit(message.getDescription());

							Message<Produit> response_message = new Message<Produit>();
							response_message.setAction(INVENTORY_SCAN);
							response_message.setResult(produit);
							clientEndPoint.sendMessage(GSON.toJson(response_message));
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
			produit = DAOFactory.getLGPIDao().getProduit(getCode(data, false));
			if (produit == null) {
				String code = getCode(data, true);
				if (code != null) {
					produit = DAOFactory.getLGPIDao().getProduit(code);	
				}
			}
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

	private static String getCode(String code, Boolean sub_code) {
		if (code.length() == 7) {
			// Code CIP on le garde tel quel
			return code;
		} else if (code.length() == 13) {
			// Code EAN on le garde tel quel
			if (sub_code) {
				return code.substring(4, 11);
			}
			return code;
		} else if (code.length() == 16) {
			// Il faut prendre le code cip après le premier chiffre
			return code.substring(1, 8);
		} else if (code.length() == 20) {
			// EAN 13 + 7
			return getCode(code.substring(0, 13), sub_code);
		} else if (code.length() > 16) {
			// Certainement un data matrix, on prend les 13 caractères après le troisième pour avoir le code ean
			return getCode(code.substring(3, 16), sub_code);
		}

		// Sinon ??? On revoit le code par defaut
		return code;
	}

	private static String getStateMessage(String state) {
		return "{\"action\":\"" + state + "\"}";
	}

}