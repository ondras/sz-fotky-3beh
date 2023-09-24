<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="head">
		<xsl:param name="title" select="''" />
		<head>
			<title>
				<xsl:choose>
					<xsl:when test="$title">
						<xsl:value-of select="$title" />
						<xsl:text> – </xsl:text>
					</xsl:when>
					<xsl:when test="@title">
						<xsl:value-of select="@title" />
						<xsl:text> – </xsl:text>
					</xsl:when>
				</xsl:choose>
				<xsl:call-template name="_"><xsl:with-param name="key" select="'title'" /></xsl:call-template>
			</title>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width" />
			<link rel="stylesheet" href="/css/style.css" />
			<link rel="alternate" type="application/rss+xml" title="RSS" href="/rss" />
		</head>
	</xsl:template>
</xsl:stylesheet>
