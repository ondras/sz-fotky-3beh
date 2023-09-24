<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_top.xsl" />
	<xsl:import href="_head.xsl" />

	<xsl:output
		method="html"
		indent="yes"
		omit-xml-declaration="yes"
		doctype-system="about:legacy-compat"
	/>

	<xsl:template name="input">
		<xsl:param name="name">a</xsl:param>
		<xsl:element name="input">
			<xsl:attribute name="type">text</xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="$name" /></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@*[name() = $name]" /></xsl:attribute>
		</xsl:element>
	</xsl:template>

	<xsl:template match="//admin/config">
	<html>
		<xsl:call-template name="head" />
		<body>
			<xsl:call-template name="top" />
			<form action="./?action=config-finish" method="post">
				<table>
					<tbody>
						<tr>
							<td>Název</td>
							<td>
								<xsl:call-template name="input"><xsl:with-param name="name">name</xsl:with-param></xsl:call-template>
							</td>
						</tr>

						<tr>
							<td>Heslo (uživatel)</td>
							<td>
								<input type="password" name="pass-user" />
							</td>
						</tr>

						<tr>
							<td>Heslo (admin)</td>
							<td>
								<input type="password" name="pass-admin" />
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Uložit" /></td>
						</tr>

					</tbody>
				</table>
			</form>
		</body>
	</html>
	</xsl:template>

</xsl:stylesheet>
