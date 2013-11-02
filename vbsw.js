jQuery(document).ready(function() {
    showWidget();
});

/**
 * Displays Search Widget
 */
function showWidget() {
    var widget = jQuery('#vex-vbsw');
    widget.html(
        '<form action="vbsw.php">'
       +'<div class="login"><input class="user" name="user" value="username" type="text"><br />'
       +'<input class="pass" name="pass" value="password" type="password"></div>'
       +'<input class="query" name="query" type="text"><input type="submit" value="Search">'
       +'</form>');
    widget.find('form').unbind();
    widget.find('form').submit(function (event) {
        event.preventDefault();
        var user = widget.find('input.user').val();
        var pass = widget.find('input.pass').val();
        var query = widget.find('input.query').val();
        var data = {
            user: user,
            pass: pass,
            query: query
        };
        jQuery.getJSON('./vbsw.php', data, function (result) {
            if(result) {
                if(result.length > 0) {
                    var html = '<table><thead><tr><th>Image</th><th>Name</th><th>Description</th></tr></thead><tbody>';
                    jQuery.each(result, function (key, book) {
                        html += '<tr><td><img src="'+book.img+'" alt="Book Cover" /></td>';
                        html += '<td><a href="'+book.url+'">'+book.name+'</a></td>';
                        html += '<td>'+book.description+'</td></tr>';
                    });
                    html += '</tbody></table>';
                    html += '<a class="search-again" href="#">Search again!</a>';
                    widget.html(html);
                    widget.find('a.search-again').click(function(event) {
                        event.preventDefault();
                        showWidget();
                    });
                }
            }
        });
    });
}