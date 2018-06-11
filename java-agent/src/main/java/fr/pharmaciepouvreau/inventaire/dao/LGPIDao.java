package fr.pharmaciepouvreau.inventaire.dao;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.springframework.jdbc.core.namedparam.MapSqlParameterSource;
import org.springframework.jdbc.core.simple.SimpleJdbcTemplate;

import fr.pharmaciepouvreau.inventaire.dto.Produit;

public class LGPIDao {

	private SimpleJdbcTemplate simpleJdbcTemplate;

	public SimpleJdbcTemplate getSimpleJdbcTemplate() {
		return simpleJdbcTemplate;
	}

	public void setSimpleJdbcTemplate(SimpleJdbcTemplate simpleJdbcTemplate) {
		this.simpleJdbcTemplate = simpleJdbcTemplate;
	}

	public Produit getProduit(String codecip) {
		// Query params
		Map<String, Object> params = new HashMap<String, Object>();
		params.put("code", codecip);

		List<Produit> produits = getSimpleJdbcTemplate().getNamedParameterJdbcOperations().query(Queries.getQuery("produit.get"),
				new MapSqlParameterSource(params), new ProduitMapper());

		if (produits.size() > 0) {
			Produit pdt = produits.get(0);
			if (pdt.getCip() == null) {
				pdt.setCip(codecip);
			}
			
			return pdt;
		}

		return null;
	}

	public List<Produit> searchProduit(String term) {
		// Query params
		Map<String, Object> params = new HashMap<String, Object>();
		params.put("term", '%' + term + '%');

		return getSimpleJdbcTemplate().getNamedParameterJdbcOperations().query(Queries.getQuery("produit.search"), new MapSqlParameterSource(params),
				new ProduitMapper());
	}

	public void updateStockProduit(Produit produit) {
		// Query params
		Map<String, Object> params = new HashMap<String, Object>();
		params.put("id", produit.getId());
		params.put("stock", produit.getStock());

		getSimpleJdbcTemplate().getNamedParameterJdbcOperations().update(Queries.getQuery("produit.set"), new MapSqlParameterSource(params));
	}

	public void eraseInventaire() {
		// Erase inventaire
		getSimpleJdbcTemplate().getNamedParameterJdbcOperations().update(Queries.getQuery("inv.delete"),
				new MapSqlParameterSource(new HashMap<String, String>()));
	}

	public void insertProduitInventaire(Produit produit) {
		// Query params
		Map<String, Object> params = new HashMap<String, Object>();
		params.put("id", produit.getId());

		getSimpleJdbcTemplate().getNamedParameterJdbcOperations().update(Queries.getQuery("inv.insert"), new MapSqlParameterSource(params));
	}
}
