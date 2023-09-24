function toggle(section) {
	section.classList.toggle("hidden");
	if (section.classList.contains("hidden")) { return; }
	[...section.querySelectorAll("img")].forEach(img => {
		img.src = img.dataset.src;
	});
}

document.addEventListener("click", e => {
	let node = e.target;
	if (node.nodeName.toLowerCase() != "h2") { return; }

	toggle(node.parentNode);
});

toggle(document.querySelector(".year"));
