package fr.pharmaciepouvreau.inventaire.dto;

import java.io.Serializable;

public class Produit implements Serializable {
	private static final long serialVersionUID = -5595782742373597861L;

	private Integer id;

	private String cip;

	private String name;

	private Integer stock;

	private Integer inventory = 1;

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getCip() {
		return cip;
	}

	public void setCip(String cip) {
		this.cip = cip;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public Integer getInventory() {
		return inventory;
	}

	public void setInventory(Integer inventory) {
		this.inventory = inventory;
	}

	public Integer getStock() {
		return stock;
	}

	public void setStock(Integer stock) {
		this.stock = stock;
	}

	public void addInventaire(Integer quantitee) {
		setInventory(getInventory() + quantitee);
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((id == null) ? 0 : id.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Produit other = (Produit) obj;
		if (id == null) {
			if (other.id != null)
				return false;
		} else if (!id.equals(other.id))
			return false;
		return true;
	}

	@Override
	public String toString() {
		return "Produit [id=" + id + ", cip=" + cip + ", name=" + name + ", stock=" + stock + ", inventory=" + inventory + "]";
	}

}
