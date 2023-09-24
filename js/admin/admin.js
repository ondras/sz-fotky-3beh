var Admin = {};

Admin.init = function() {
	var all = document.getElementsByTagName("a");
	for (var i=0;i<all.length;i++) {
		var a = all[i];
		if (OZ.DOM.hasClass(a, "confirm")) {
			OZ.Event.add(a, "click", Admin.confirm);
		}
	}

	var photo = OZ.$("photo");
	if (photo) { new Admin.Photo(photo); }

	var all = document.getElementsByTagName("input");
	var arr = [];
	for (var i=0;i<all.length;i++) { arr.push(all[i]); }
	all = arr;
	var firstDate = null;
	for (var i=0;i<all.length;i++) {
		var input = all[i];
		if (OZ.DOM.hasClass(input, "date")) {
			if (!firstDate) {
				firstDate = input;
			} else {
				var tmp = input;
				var btn = OZ.DOM.elm("input", {type:"button", value:"⇓"});
				firstDate.parentNode.appendChild(btn);
				OZ.Event.add(btn, "click", function(e) { tmp.value = firstDate.value; });
			}
		}
	}
}

Admin.confirm = function(e) {
	var result = confirm("O'RLY?");
	if (!result) { OZ.Event.prevent(e); }
}

Admin.Photo = function(input) {
	var btn = OZ.DOM.elm("input", {type:"button",value:"Vybrat"});
	this._input = input;
	this._btn = btn;
	this._div = null;
	this._images = [];
	input.parentNode.appendChild(btn);
	OZ.Event.add(btn, "click", this.request.bind(this));
	OZ.Event.add(document, "click", this.close.bind(this));
}

Admin.Photo.prototype.request = function(e) {
	OZ.Event.stop(e);
	this.close();
	var id = document.location.href.match(/id=([0-9]+)/)[1];
	OZ.Request("./?action=images&id="+id, this.response.bind(this), {xml:true});
}

Admin.Photo.prototype.response = function(xmlDoc) {
	var pos = OZ.DOM.pos(this._btn);
	var div = OZ.DOM.elm("div", {position: "absolute", left:pos[0]+"px", top:pos[1]+"px", id:"images"});
	OZ.Event.add(div, "click", this.click.bind(this));
	var images = xmlDoc.getElementsByTagName("image");
	for (var i=0;i<images.length;i++) {
		var image = images[i];
		var node = OZ.DOM.elm("img");
		node.src = "/"+image.getAttribute("url");
		if (image.getAttribute("name") == this._input.value) { OZ.DOM.addClass(node, "selected"); }
		div.appendChild(node);
		this._images.push([node, image.getAttribute("name")]);
	}

	this._div = div;
	var body = document.getElementsByTagName("body")[0];
	body.appendChild(div);
}

Admin.Photo.prototype.click = function(e) {
	var img = OZ.Event.target(e);
	if (img.nodeName.toLowerCase() != "img") { return; }
	
	for (var i=0;i<this._images.length;i++) {
		var item = this._images[i];
		if (item[0] == img) { this._input.value = item[1]; }
	}
}

Admin.Photo.prototype.close = function() {
	if (!this._div) { return; }
	this._div.parentNode.removeChild(this._div);
	this._div = null;
	this._images = [];
}

OZ.Event.add(window, "load", Admin.init);

