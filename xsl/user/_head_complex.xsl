<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="head">
		<head>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width" />
			<title>
				<xsl:value-of select="@title"/>
				<xsl:text> – </xsl:text>
				<xsl:call-template name="_"><xsl:with-param name="key" select="'title'" /></xsl:call-template>
			</title>
			<link rel="stylesheet" href="/css/style.css?1" />
			<link rel="alternate" type="application/rss+xml" title="RSS" href="/rss" />
		</head>
	</xsl:template>
</xsl:stylesheet>
