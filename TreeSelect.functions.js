// TreeSelectTV version 0.1.1 for MODx Evolution 

var FolderSelect = new Class({
    initialize: function(inputID,tree,inputStatus,filesOnly,imageView,hideOnSelect) {
        // Get parameters
        this.name = inputID;
        this.input = $(inputID);
        this.filesOnly = filesOnly;
        this.inputStatus = inputStatus;
        this.imageView = imageView;
        this.hideOnSelect = hideOnSelect;

        // Hide main input field
        this.input.setStyle('display','none');

        if (this.inputStatus != "") {
            // Set new result field
            this.display = new Element('span', { 'id':'treeBoxOutput_'+this.name, 'class':'treeBox_output '+this.inputStatus });
            this.display.innerHTML = this.input.value;
                                                  
        }
        // Create new elemnts
        this.box = new Element('div',{'id':'treeBox_'+this.name,'class':'treeBox'});
        if (this.imageView) this.image = new Element('div',{'id':'treeBoxImage_'+this.name,'class':'treeBox_image'});
        // Put HTML code
        this.box.innerHTML = tree;
        
        // Put input value to the tree
        this.selectors = this.box.getElements('.selector');
        for (var i=0; i < this.selectors.length; i++) {
            var selector_line = this.selectors[i];
            while (!selector_line.hasClass('item_line')) selector_line = this.selectors[i].getParent();
            if (selector_line.getProperty('path') == this.input.value) {
                selector_line.addClass('new_select');
                // Close selected branch ...
                selector_line.addClass('close');
            }
        }
        // ... and open it again
        this.checkBranch();
        
        // Set event behavior for the button
        if (inputStatus == "toggle") {
            this.box.addClass('hide');
            this.display.set({
                'events': {
                    mouseover: function() { this.addClass('hover'); },
                    mouseleave: function() { this.removeClass('hover'); },
                    click: function() { this.box.toggleClass('hide'); }.bind(this)
                }                
            });
        }
        // Set event behavior for items
        this.selectors.set({
		    'events': {
                mouseover: function() { this.addClass('hover'); },
                mouseleave: function() { this.removeClass('hover'); },
                click: function() {
                    var selector_line = this;
                    while (!selector_line.hasClass('item_line')) selector_line = this.getParent();
                    if (!selector_line.hasClass('new_select')) selector_line.addClass('new_select');
                }
            }
		});
		// Set event bahavior for the box
        this.box.set({
            'events': {
                click: function() {
                    var new_select = this.box.getElements('.new_select');
                    if (new_select.length) { this.checkBranch(); }
                }.bind(this)
            }
        });

        // Add the new elements to the table cell
        if (this.imageView) this.input.getParent().adopt(this.image);
        if (this.inputStatus != "") this.input.getParent().adopt(this.display);
        this.input.getParent().adopt(this.box);
    },

    checkBranch: function() {
        var new_select = this.box.getElements('.new_select');
        if (new_select.length) {
            this_line = new_select[0];
            this_line.removeClass('new_select');

            // Set value to input field
            if (this_line.hasClass('file') || (this_line.hasClass('folder') && (this.filesOnly == false))) {
                this.input.value = this_line.getProperty('path');
                if (this.inputStatus !== "") this.display.innerHTML = this_line.getProperty('path');
                if ((this.inputStatus == "toggle") && this.hideOnSelect) this.box.toggleClass('hide');
            }
            if (this.imageView) {
                // Show image preview
                var img = this_line.getProperty('img');
                if (img.length) this.image.innerHTML = '<img src="'+img+'">';
                else if (this_line.hasClass('file') || (this.filesOnly == false)) this.image.innerHTML = '';
            }

            // Hide branch if already selected or marked to be closed
            if (this_line.hasClass('open') && (!this_line.hasClass('selected') || this_line.hasClass('close')))
                this_line.removeClass('open');

            // Select item
            this.box.getElements('.selected').removeClass('selected');
            this_line.addClass('selected');

            // Hide all other branches
            this.checkHideGroups();
            
            // Show all parents
            var parent = this_line;
            while (!parent.hasClass('level_1')) {
                if (parent.hasClass('hide')) parent.removeClass('hide');
                parent = parent.getParent();
            }

            // Show children
            if (!this_line.hasClass('last_item')) {
                if (!this_line.hasClass('close')) this_line.toggleClass('open');
                var child_group = this_line.getElements('.item_group');
                if (this_line.hasClass('open') && child_group[0].hasClass('hide')) {
                    child_group[0].removeClass('hide');
                } else if (!this_line.hasClass('open') && !child_group[0].hasClass('hide')) {
                    child_group[0].addClass('hide');
                }
            }
            this.box.getElements('.close').removeClass('close');
            this.checkTogglers();
            
        } else this.checkHideGroups();
    },
    checkHideGroups: function() {
        var item_groups = this.box.getElements('.item_group');
        for (var i=0; i < item_groups.length; i++) {
            if (!item_groups[i].hasClass('hide') && !item_groups[i].hasClass('level_1')) item_groups[i].addClass('hide');
        }
    },
    checkTogglers: function() {
        // Set togglers to actual state
        var toggler = this.box.getElements('.toggler');
        for (var i=0; i < toggler.length; i++) {
            parent = toggler[i];
            while (!parent.hasClass('item_line')) parent = parent.getParent();
            var child_group = parent.getElements('.item_group');
            if (child_group.length) {
                if (child_group[0].hasClass('hide')) {
                    if (toggler[i].hasClass('open')) toggler[i].removeClass('open');
                } else {
                    if (!toggler[i].hasClass('open')) toggler[i].addClass('open');
                }
            }
        }
    }
});
