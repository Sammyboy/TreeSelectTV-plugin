// TreeSelectTV for MODx Evolution

var FolderSelect = new Class({
    initialize: function(inputID,tree,inputStatus,filesOnly) {
        this.name = inputID;
        this.input = $(inputID);
        this.filesOnly = filesOnly;
        if (inputStatus == "") this.input.setStyle('display','none');
        if (inputStatus != "edit") this.input.setProperty('readonly','readonly');
        this.box = new Element('div',{'id':'treeBox_'+this.name,'class':'treeBox'});
        this.box.innerHTML = tree;

        this.selectors = this.box.getElements('.selector');
        for (var i=0; i < this.selectors.length; i++) {
            var selector_line = this.selectors[i].getParent();
            while (!selector_line.hasClass('item_line')) selector_line = this.selectors[i].getParent();
            if (selector_line.getProperty('path') == this.input.value) {
                selector_line.addClass('new_select');
                // Hide selected branch
                selector_line.addClass('close');
            }
        }
        this.checkBranch();

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
        this.box.set({
            'events': {
                click: function() {
                    var new_select = this.box.getElements('.new_select');
                    if (new_select.length) { this.checkBranch(); }
                }.bind(this)
            }
        });
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
            }
            // Hide branch if already selected or marked to be closed
            if (this_line.hasClass('open') && (!this_line.hasClass('selected') || this_line.hasClass('close'))) this_line.removeClass('open');

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

            // Show branches
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


window.addEvent('domready', function() {
    var tvIds       = [[+tvIds+]];
    var trees       = [[+htmlTrees+]];
    var inputStatus = [[+inputStatus+]];
    var filesOnly   = [[+files_only+]];

    for (var i=0; i<tvIds.length; i++) {
        var inputID = 'tv'+ tvIds[i];
        if ($(inputID) != null) { 
            var modxFolderSelect = new FolderSelect(inputID,trees[i],inputStatus[i],filesOnly[i]);
        }
    }
    
});
