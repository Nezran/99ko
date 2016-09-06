function updateHtaccess(rewriteBase){
        var checked = $('#urlRewriting')[0].checked;
        if(!checked) $('#htaccess').html('Options -Indexes');
	else{
		var content = "Options -Indexes\nOptions +FollowSymlinks\nRewriteEngine On\nRewriteBase "+rewriteBase+"\nRewriteRule ^admin/$  admin/ [L]\nRewriteRule ^([a-z]+)/([a-z-0-9,./]+).html$  index.php?p=$1&param=$2 [L]\nRewriteRule ^([a-z]+)/$  index.php?p=$1 [L]";
                $('#htaccess').html(content);
	}
}