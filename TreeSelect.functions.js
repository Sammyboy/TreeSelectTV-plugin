// TreeSelectTV version 0.1.1 for MODx Evolution 

var FolderSelect = new Class({
    initialize: function(inputID,tree,inputStatus,filesOnly,imageView,hideOnSelect) {
        // get parameters
        this.name = inputID;
        this.input = $(inputID);
        this.filesOnly = filesOnly;
        this.inputStatus = inputStatus;
        this.imageView = imageView;
        this.hideOnSelect = hideOnSelect;

        // hide main input field
        this.input.setStyle('display','none');

        if (this.inputStatus != "") {
            // set new result field
            this.display = new Element('span', { 'id':'treeBoxOutput_'+this.name, 'class':'treeBox_output '+this.inputStatus });
            this.display.innerHTML = this.input.value;
                                                  
        }
        // create new elemnts
        this.box = new Element('div',{'id':'treeBox_'+this.name,'class':'treeBox'});
        if (this.imageView) this.image = new Element('div',{'id':'treeBoxImage_'+this.name,'class':'treeBox_image'});
        // put HTML code
        this.box.innerHTML = tree;
        
        // put input value to the tree
        this.selectors = this.box.getElements('.selector');
        this.togglers = this.box.getElements('.toggler');
        for (var i=0; i < this.selectors.length; i++) {
            var selector_line = this.selectors[i];
            while (!selector_line.hasClass('item_line')) selector_line = this.selectors[i].getParent();
            if (selector_line.getProperty('path') == this.input.value) {
                selector_line.addClass('new_select');
                // close selected node ...
                selector_line.addClass('close');
            }
        }
        // ... and open it again
        this.checkList();
        
        // set event behavior for the button
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
        // set event behavior for items
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
		// set event behavior for togglers
		this.togglers.set({
		    'events': {
                mouseover: function() { this.addClass('hover'); },
                mouseleave: function() { this.removeClass('hover'); },
                click: function() {
                    var selector_line = this;
                    while (!selector_line.hasClass('item_line')) selector_line = this.getParent();
                    if (!selector_line.hasClass('new_toggle')) selector_line.addClass('new_toggle');
                }
            }
		});
		// set event bahavior for the box
        this.box.set({
            'events': {
                click: function() {
                    var new_select = this.box.getElements('.new_select');
                    if (new_select.length) { this.checkList(); }
                    var new_toggle = this.box.getElements('.new_toggle');
                    if (new_toggle.length) { this.toggleNode(); }
                }.bind(this)
            }
        });
        
        // add the new elements to the table cell
        if (this.imageView) this.input.getParent().adopt(this.image);
        if (this.inputStatus != "") this.input.getParent().adopt(this.display);
        this.input.getParent().adopt(this.box);
    },

    checkList: function() {
        var new_select = this.box.getElements('.new_select');
        if (new_select.length) {
            this.line = new_select[0];
            this.line.removeClass('new_select');

            // set value to input field
            if (this.line.hasClass('file') || (this.line.hasClass('folder') && (this.filesOnly == false))) {
                this.input.value = this.line.getProperty('path');
                if (this.inputStatus !== "") this.display.innerHTML = this.line.getProperty('path');
                if ((this.inputStatus == "toggle") && this.hideOnSelect) this.box.toggleClass('hide');
            }
            if (this.imageView) {
                // show image preview
                var img = this.line.getProperty('img');
                if (img.length) this.image.innerHTML = '<img src="'+img+'">';
                else if (this.line.hasClass('file') || (this.filesOnly == false)) this.image.innerHTML = '';
            }

            // hide all other groups
            this.checkHideGroups();            

            // show all parents
            var parent = this.line;
            while (!parent.hasClass('level_1')) {
                if (parent.hasClass('hide')) parent.removeClass('hide');
                if (parent.hasClass('item_line') && !parent.hasClass('open')) parent.addClass('open');
                parent = parent.getParent();
            }
            
            // hide branch if already selected or marked to be closed
            if (this.line.hasClass('open') && (!this.line.hasClass('selected') || this.line.hasClass('close')))
                this.line.removeClass('open');
            else this.line.toggleClass('open');

            // select item
            this.box.getElements('.selected').removeClass('selected');
            this.line.addClass('selected');
                        
            this.toggleSubNodes();
            this.box.getElements('.close').removeClass('close');
            this.checkTogglers();
            
        } else this.checkHideGroups();
    },
    toggleNode: function() {
        // show or hide nodes
        var new_toggle = this.box.getElements('.new_toggle');
        
        if (new_toggle.length) {
            this.line = new_toggle[0];
            new_toggle.removeClass('new_toggle');
            if (this.line.hasClass('close')) this.line.removeClass('close');

            this.toggleSubNodes();
            this.checkTogglers();
            
        }
    },
    toggleSubNodes: function() {
        // show or hide subnodes
        if (!this.line.hasClass('last_item')) {
            if (!this.line.hasClass('close')) this.line.toggleClass('open');
            var child_group = this.line.getElements('.item_group');
            if (this.line.hasClass('open') && child_group[0].hasClass('hide')) {
                child_group[0].removeClass('hide');
            } else if (!this.line.hasClass('open') && !child_group[0].hasClass('hide')) {
                child_group[0].addClass('hide');
            }
        }
    },
    checkHideGroups: function() {
        var item_groups = this.box.getElements('.item_group');
        for (var i=0; i < item_groups.length; i++) {
            if (!item_groups[i].hasClass('hide') && !item_groups[i].hasClass('level_1')) item_groups[i].addClass('hide');
        }
    },
    checkTogglers: function() {
        // adjust togglers
        if (this.togglers.length) {
            for (var i=0; i < this.togglers.length; i++) {
                parent = this.togglers[i];
                while (!parent.hasClass('item_line')) parent = parent.getParent();
                var child_group = parent.getElements('.item_group');
                if (child_group.length) {
                    if (child_group[0].hasClass('hide')) {
                        if (this.togglers[i].hasClass('open')) this.togglers[i].removeClass('open');
                    } else {
                        if (!this.togglers[i].hasClass('open')) this.togglers[i].addClass('open');
                    }
                }
            }
        }
    }
});
