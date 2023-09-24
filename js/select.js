var Select = function(select) {
	this._selectedIndex = select.selectedIndex;
	this._opened = false;
	this._dom = {
		list: null,
		parent: select.parentNode,
		items: []
	};

	var options = this._scan(select);
	this._build(options);
	this._dom.parent.removeChild(select);

	document.addEventListener("mousedown", e => this._close(e));
	window.addEventListener("keydown", e => this._keydown(e));
}

Select.prototype._scan = function(select) {
	var result = [];
	var options = select.getElementsByTagName("option");
	for (var i=0;i<options.length;i++) {
		result.push(options[i]);
	}
	return result;
}

Select.prototype._build = function(options) {
	var list = document.createElement("div");
	this._dom.list = list;

	for (var i=0;i<options.length;i++) {
		var item = options[i];
		var a = document.createElement("a");
		a.href = item.value;
		if (i == this._selectedIndex) { a.classList.add("selected"); }
		a.innerHTML = item.innerHTML;
		this._dom.items.push(a);
		list.appendChild(a);
	}

	this._dom.parent.appendChild(list);
	this._dom.parent.addEventListener("click", e => this._open(e));
	this._dom.list.addEventListener("mousedown", e => e.stopPropagation());
}

Select.prototype._open = function(e) {
	if (this._opened) { return; }
	this._opened = true;
	e.preventDefault();
	this._dom.parent.classList.add("open");
	this._dom.items[this._selectedIndex].focus();
}

Select.prototype._close = function(e) {
	if (!this._opened) { return; }
	this._opened = false;

	this._dom.parent.classList.remove("open");
}

Select.prototype._keydown = function(e) {
	if (e.keyCode == 27) { this._close(e); }
}

