async function init() {
	let selects = document.querySelectorAll("#album select, #chapter select");
	for (let i=0; i<selects.length; i++) { new Select(selects[i]); }

	let g = document.querySelector("oz-gallery");
	g.addEventListener("close", _ => {
		history.replaceState(null, "", "#");
	});

	g.addEventListener("change", _ => {
		history.replaceState(null, "", `#${g.index}`);
	});

	if (location.hash) {
		let promises = ["oz-gallery" , "little-planet"].map(name => customElements.whenDefined(name));
		await Promise.all(promises);
		let i = Number(location.hash.substring(1));
		g.show(i);
	}
}

init();

