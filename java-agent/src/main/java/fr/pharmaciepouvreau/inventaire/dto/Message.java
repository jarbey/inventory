package fr.pharmaciepouvreau.inventaire.dto;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

public class Message<T> implements Serializable {
	private static final long serialVersionUID = 5813063486110672710L;

	private String description;

	private String action;

	private List<T> results = new ArrayList<T>();

	private T result;

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public String getAction() {
		return action;
	}

	public void setAction(String action) {
		this.action = action;
	}

	public List<T> getResults() {
		return results;
	}

	public void setResults(List<T> results) {
		this.results = results;
	}

	public void addResult(T result) {
		this.results.add(result);
	}

	public T getResult() {
		return result;
	}

	public void setResult(T result) {
		this.result = result;
	}

}
