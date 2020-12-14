// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for the quizaccess_MProctoring plugin.
 *
 * @package    quizaccess_MProctoring
 * @author     MEETCS (admin@meetcs.com)
 *             Atul (atul.adhikari@camplus.co.in)
 *             Rushab (rushab.ambre@camplus.co.in)
 *             Abhishek (abhishek.ambokar@camplus.co.in)
 * @copyright  Meetcs@2020
 */
require.config( {
    paths: {
        'datatables.net': '//cdn.datatables.net/1.10.21/js/jquery.dataTables.min',
    }
} );
function loadCss(url) {
    var link = document.createElement("link");
    link.type = "text/css";
    link.rel = "stylesheet";
    link.href = url;
    document.getElementsByTagName("head")[0].appendChild(link);
}
define(['jquery','datatables.net'], function($) {
    return {
        init: function(data) {
            loadCss("//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css");
            $('#abc').append(
                $('</br>') ,
                $('<table/>' , {
                    'id':"dataUser" ,
                    'class' : "table table-striped table-bordered" ,
                    'width':"100%"
                }));
                $('#dataUser').DataTable( {
                    data: data,
                    columns: [
                        { title: " " },
                        { title: "Firstname / Lastname" },
                        { title: "Email" },
                        { title: "Attempt" },
                        { title: "Browser History" },
                        { title: "Out of Focus" }
                    ]
                });
        }
    };
});