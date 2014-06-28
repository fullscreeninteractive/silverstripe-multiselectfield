(function($){

    // Initialise multiselect field
    multiSelectFieldInitialise = function(){
        var multiSelectField = $(this);
        var sourceField = $('select', multiSelectField);
        
        // Add source, destination and control fields
        sourceField.addClass('hidden');
        sourceField.before('<select class="multiselect-unselected" multiple="multiple"></select>');
        sourceField.before('<div class="multiselect-controls"><p><button class="action multiselect-add">Add &gt;</button></p><p><button class="action multiselect-remove">&lt; Remove</button></p></div>');
        sourceField.before('<select class="multiselect-selected" multiple="multiple"></select>');
        
        // Move unselected items to source copy selected items to dest
        var selectedField = $('.multiselect-selected', multiSelectField);
        var unselectedField = $('.multiselect-unselected', multiSelectField);
        $('option:not(:selected)', sourceField).appendTo(unselectedField);
        $('option:selected', sourceField).clone().appendTo(selectedField).attr('selected', '');
        
        // Configure controls
        $('.multiselect-add', multiSelectField).click(function(){
            $('option:selected', unselectedField).appendTo(selectedField).attr('selected', '');
            $('option', sourceField).remove();
            sourceField.append($('option', selectedField).clone());
            $('option', sourceField).attr('selected', 'selected');
            return false;
        });
        $('.multiselect-remove', multiSelectField).click(function(){
            $('option:selected', selectedField).appendTo(unselectedField).attr('selected', '');
            $('option', sourceField).remove();
            sourceField.append($('option', selectedField).clone());
            $('option', sourceField).attr('selected', 'selected');
            return false;
        });
    }
    
    if (typeof $(document).livequery != 'undefined') {
        $('.multiselect').livequery(multiSelectFieldInitialise);
    }
    else {
        $(document).ready(function(){
            $('.multiselect').each(multiSelectFieldInitialise);
        });
    }
    
})(jQuery);
