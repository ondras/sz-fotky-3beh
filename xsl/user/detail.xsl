<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_head_complex.xsl" />
	<xsl:import href="../_.xsl" />
	<xsl:param name="language" select="'cz'" />

	<xsl:output
		method="html"
		indent="yes"
		omit-xml-declaration="yes"
		doctype-system="about:legacy-compat"
	/>

	<xsl:template match="//user">
	<html>
		<xsl:call-template name="head" />
		<body id="detail">

		<header>

			<section id="album" class="pod">
				<a href="#">
					<h2>
					<xsl:for-each select="albums/album[@selected='1']">
							<xsl:value-of select="@name" />
							<xsl:text> (</xsl:text>
							<xsl:value-of select="@start" />
							<xsl:if test="@start != @end"> &#x2013; <xsl:value-of select="@end" /></xsl:if>
							<xsl:text>)</xsl:text>
					</xsl:for-each>
					</h2>
				</a>

				<xsl:if test="albums[@authorized=1]">
				<select hidden="hidden">
					<option value="/{year}">···</option>
					<xsl:for-each select="albums/album">
						<xsl:element name="option">
							<xsl:attribute name="value"><xsl:value-of select="@href" /></xsl:attribute>
							<xsl:if test="@selected = '1'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="@name" />
							<xsl:text> (</xsl:text>
							<xsl:value-of select="@start" />
							<xsl:if test="@start != @end"> &#x2013; <xsl:value-of select="@end" /></xsl:if>
							<xsl:text>)</xsl:text>
						</xsl:element>
					</xsl:for-each>
					<option value="/{year}">···</option>
				</select>
				</xsl:if>
			</section>

			<xsl:if test="chapters">
				<section id="chapter" class="pod">
					<a href="#">
						<h2>
						<xsl:for-each select="chapters/chapter[@selected='1']">
							<xsl:choose>
								<xsl:when test="@name != ''"><xsl:value-of select="@name" /></xsl:when>
								<xsl:otherwise>(<xsl:call-template name="_"><xsl:with-param name="key" select="'allchapters'" /></xsl:call-template>)</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
						</h2>
					</a>

					<xsl:if test="chapters/chapter">
					<select hidden="hidden">
						<xsl:for-each select="chapters/chapter">
							<xsl:element name="option">
								<xsl:attribute name="value"><xsl:value-of select="@href" /></xsl:attribute>
								<xsl:if test="@selected = '1'">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:choose>
									<xsl:when test="@name != ''"><xsl:value-of select="@name" /></xsl:when>
									<xsl:otherwise>(<xsl:call-template name="_"><xsl:with-param name="key" select="'allchapters'" /></xsl:call-template>)</xsl:otherwise>
								</xsl:choose>
							</xsl:element>
						</xsl:for-each>
					</select>
					</xsl:if>
				</section>
			</xsl:if>
		</header>

		<main>
			<a id="back" href="/{year}">🏠</a>
			<oz-gallery>
				<xsl:for-each select="thumbnails/thumbnail">
					<a href="{@big}" data-type="{@type}"><img src="{@url}" alt="" /></a>
				</xsl:for-each>
			</oz-gallery>
		</main>

		<script src="/js/select.js"></script>
		<script type="module" src="/js/detail.js?3"></script>
		<script type="module" src="https://cdn.jsdelivr.net/gh/ondras/oz-gallery/oz-gallery.js?3"></script>
		<script type="module" src="https://unpkg.com/little-planet@0.5"></script>
		</body>
	</html>
	</xsl:template>
</xsl:stylesheet>
