
jQuery(document).ready(function () {    
    //fonction de clonage prototype des formulaire twig
    jQuery('.add-another-collection-widget').click(function (e) {
        var list = jQuery(jQuery(this).attr('data-list-selector'));
        var counter = list.data('widget-counter') || list.children().length;
        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data('widget-counter', counter);
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
        addTagForm();
    });


});

//lien "supprimer cette balise" à chaque formulaire
function addTagForm() {

    $collectionHolder = $('ul.tags');

    $collectionHolder.find('li').each(function() {
        console.log($(this).children("button").length)
        if(!$(this).children("button").length){
             addTagFormDeleteLink($(this));
        }
    });  
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormButton = $('<button type="button">Supprimer</button>');
    $tagFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
       $tagFormLi.remove();
    });
}
