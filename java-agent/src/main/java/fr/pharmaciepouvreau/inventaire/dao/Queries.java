package fr.pharmaciepouvreau.inventaire.dao;

import java.io.IOException;
import java.util.Properties;

public class Queries {
	private static Properties prop;

	private static void loadQueries() {
		try {
			prop = new Properties();
			prop.load(Queries.class.getClassLoader().getResourceAsStream("sqlqueries.properties"));
		} catch (IOException e) {
			System.out.println("Error while parsing properties : " + e.getMessage());
		}
	}

	/**
	 * Get query
	 * 
	 * @param key
	 * @param args
	 * @return
	 */
	public static String getQuery(String key) {
		if (prop == null) {
			Queries.loadQueries();
		}
		return prop.getProperty(key);
	}
}
