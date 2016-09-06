function openPluginConfig() {
	$('#pluginConfig').show(200);
	$('#pluginConfigButton').unbind('click', openPluginConfig);
	$('#pluginConfigButton').click(closePluginConfig);
}
function closePluginConfig() {
	$('#pluginConfig').hide(200);
	$('#pluginConfigButton').unbind('click', closePluginConfig);
	$('#pluginConfigButton').click(openPluginConfig);
}
$(document).ready(function () {
	$('#pluginConfigButton').click(openPluginConfig);
	$('.tabs').each(function(){
		// Track l'onglet actif ainsi que sont contenu
		var $active, $content, $links = $(this).find('a');
		// Si le location.hash correspond  l'un des liens, l'utiliser comme onglet actif.
	// Si aucune correspondance n'est trouve, donner le premier lien de l'onglet en tant qu'actif initial.
		$active = $($links.filter('[href="'+localStorage['activeTab']+'"]')[0] || $links[0]);
		$active.addClass('active');
		$content = $($active.attr('href'));
		// Cacher le contenu restant
		$links.not($active).each(function () {
			$($(this).attr('href')).hide();
		});
		// Associe une action au clique
		$(this).on('click', 'a', function(e){
			// Rend l'ancien onglet inactif.
			$active.removeClass('active');
			$content.hide();
			// Met  jour les variables avec le nouveau lien et le contenu
			$active = $(this);
			$content = $($(this).attr('href'));
			// onglet cliqu => local storage
			localStorage['activeTab'] = $(this).attr('href');
			//alert(localStorage['activeTab']);
			// Cre l'onglet actif.
			$active.addClass('active');
			$content.show();
			// Prevent the anchor's default click action
			e.preventDefault();
		});
	});
	// Compteur de notifications
	var nbNotifs = 0;
	$("#notifs .alert-box").each(function(){
		nbNotifs++;
	});
	if(nbNotifs == 0){
		$(".notifsNumber").hide();
	}
	else{
		$(".notifsNumber span").html(nbNotifs);
		$(".notifsNumber").show();
	}
	// Hide notifications
	$("#alert .alert-box").delay(5000).fadeOut('slow');
});