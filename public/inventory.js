function replaceAll(string, find, replace) {
	return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

function mergeOptions(obj1, obj2) {
	var obj3 = {};
	for (var attrname in obj1) {
		obj3[attrname] = obj1[attrname];
	}
	for (var attrname in obj2) {
		obj3[attrname] = obj2[attrname];
	}
	return obj3;
}

if (typeof String.prototype.startsWith != 'function') {
	String.prototype.startsWith = function( str ) {
		return this.substring( 0, str.length ) === str;
	}
};
if (typeof String.prototype.endsWith != 'function') {
	String.prototype.endsWith = function( str ) {
		return this.substring( this.length - str.length, this.length ) === str;
	}
};
if (typeof String.prototype.ucFirst != 'function') {
	String.prototype.ucFirst = function( ) {
		return this.charAt(0).toUpperCase() + this.slice(1);
	}
};

var dataTablesDefaultOptions = {
	stateSave: false,
	buttons: [
		{
			extend:    'copy',
			text:      '<i class="fa fa-files-o"></i>',
			titleAttr: 'Copier'
		},
		{
			extend:    'excel',
			text:      '<i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel'
		},
		{
			extend:    'pdf',
			text:      '<i class="fa fa-file-pdf-o"></i>',
			titleAttr: 'PDF'
		},
		{
			extend:    'print',
			text:      '<i class="fa fa-print"></i>',
			titleAttr: 'Imprimer'
		}
	],
	bPaginate: false,
	bFilter: false,
	bInfo: false,
    lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"] ],
	language: {
		thousands: " ",
		processing: "Traitement en cours...",
		search: "Rechercher&nbsp;:",
		lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
		info: "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur <b>_TOTAL_ &eacute;l&eacute;ment(s)</b>",
		infoEmpty: "<b>Aucun résultat !</b>",
		infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
		infoPostFix: "",
		loadingRecords: "Chargement en cours...",
		zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
		emptyTable: "Aucune donnée disponible dans le tableau",
		paginate: {
			first: "Premier",
			previous: "Pr&eacute;c&eacute;dent",
			next: "Suivant",
			last: "Dernier"
		},
		aria: {
			sortAscending: ": activer pour trier la colonne par ordre croissant",
			sortDescending: ": activer pour trier la colonne par ordre décroissant"
		}
	}
};

function getDataTablesAjaxCallDefaultOptions(data, settings, updateAjaxCallOptions) {
	var options = {
		count: (data.length != 10) ? data.length : undefined,
		start: (data.start > 0) ? data.start : undefined,
		term: (data.search.value != '') ? data.search.value : undefined,
		sort: (data.order.length > 0) ? settings.aoColumns[data.order[0].column].data : undefined,
		order: (data.order.length > 0) ? data.order[0].dir : undefined,
	};

	if ( typeof updateAjaxCallOptions == 'function' ) {
		return mergeOptions(options, updateAjaxCallOptions());
	}
	return options;
}

function getPagerServerDataTablesDefaultOptions(apiUrl, updateEntry, loaded, updateAjaxCallOptions, withButtons) {
	return mergeOptions(dataTablesDefaultOptions, {
		serverSide: true,
		responsive: true,
		processing: true,
		deferRender: true,
		bPaginate: true,
		bFilter: true,
		bInfo: true,
		ajax: function (data, callback, settings) {
			var api = this.api();

			var dt = api.context[0];

			if ((dt.currentAjaxCall != null) && (typeof dt.currentAjaxCall.abort == 'function')) {
				dt.currentAjaxCall.abort();
			}
			dt.currentAjaxCall = $.get(apiUrl, getDataTablesAjaxCallDefaultOptions(data, settings, updateAjaxCallOptions), function (res) {
				if (updateEntry != null) {
					res.infos.forEach(updateEntry);
				}

				callback({
					recordsTotal: res.size,
					recordsFiltered: res.size,
					data: res.infos
				});

				if (loaded != null) {
					loaded(res, data);
				}
				dt.currentAjaxCall = null;
			});
		},
	});
}