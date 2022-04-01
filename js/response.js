let $src_link = $('#source-link');
let $res_link = $('#result-link');

$(document).ready(function () {

    $('.send').on('click', function () {

        /**/
        let source_link = $src_link.val();
        let regexp = new RegExp(/^(https?|chrome):\/\/[^\s$.?#].[^\s]*$/gm);
        if (!regexp.test(source_link)) {
            alert('wrong url');
            return;
        }

        /**/
        $.post({
            url: 'get.php',
            data: {
                source_link: source_link
            },
            dataType: 'json',
            cache: false,
            error: function (xhr, status, error) {
                //console.log(xhr.responseText);
                alert(xhr.responseText)
            },
        }).done(function (response) {
            $res_link.val(response.result_url);
        });

    });

});