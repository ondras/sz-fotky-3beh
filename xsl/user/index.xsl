<?xml version="1.0" ?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_head_simple.xsl" />
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

		<body id="index">
			<h1>
				<xsl:call-template name="_"><xsl:with-param name="key" select="'title'" /></xsl:call-template>
			</h1>

			<xsl:for-each select="years/year">
				<xsl:variable name="year" select="." />
				<section class="year hidden">
					<h2><xsl:value-of select="." /></h2>
					<ul>
						<xsl:for-each select="../../albums/album">
							<xsl:if test="$year = @year">
								<xsl:variable name="title">
									<xsl:value-of select="@name" />
									<xsl:text> (</xsl:text>
									<xsl:value-of select="@start" />
									<xsl:if test="@start != @end">
									<xsl:text> – </xsl:text>
										<xsl:value-of select="@end" />
									</xsl:if>
									<xsl:text>)</xsl:text>
								</xsl:variable>
								<li>
									<a href="{@href}">
										<img data-src="{@url}" title="{$title}" />
										<span><xsl:value-of select="$title" /></span>
									</a>
								</li>
							</xsl:if>
						</xsl:for-each>
					</ul>
				</section>
			</xsl:for-each>

			<script src="/js/index.js"></script>
		</body>
	</html>
	</xsl:template>
</xsl:stylesheet>
