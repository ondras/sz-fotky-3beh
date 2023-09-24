<?xml version="1.0" ?>
<!-- HTML hlavicka -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_title.xsl" />
	<xsl:template name="head">
		<head>
			<title><xsl:call-template name="title" /> – Správa fotogalerie</title>
			<link rel="stylesheet" type="text/css" href="/css/admin/style.css" />
			<script type="text/javascript" src="/js/oz.js"></script>
			<script type="text/javascript" src="/js/admin/admin.js"></script>
		</head>
	</xsl:template>
</xsl:stylesheet>
