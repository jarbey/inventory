package fr.pharmaciepouvreau.inventaire.dao;

import java.sql.ResultSet;
import java.sql.SQLException;

import org.springframework.jdbc.core.RowMapper;

import fr.pharmaciepouvreau.inventaire.dto.Produit;

public class ProduitMapper implements RowMapper<Produit> {

	public Produit mapRow(ResultSet rs, int rowNum) throws SQLException {
		Produit produit = new Produit();
		produit.setId(rs.getInt("id"));
		produit.setCip(rs.getString("codecip"));
		produit.setStock(rs.getInt("stock"));
		produit.setName(rs.getString("designation"));
		return produit;
	}

}
