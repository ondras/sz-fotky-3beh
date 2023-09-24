<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="_top.xsl" />
	<xsl:import href="_head.xsl" />
	<xsl:import href="_item.xsl" />
	<xsl:import href="_input.xsl" />

	<xsl:output
		method="html"
		indent="yes"
		omit-xml-declaration="yes"
		doctype-system="about:legacy-compat"
	/>

	<xsl:template match="//admin/album">
	<xsl:variable name="id_album" select="@id" />
	<html>
		<xsl:call-template name="head" />
		<body>
			<xsl:call-template name="top" />

			<form method="post" action="./?action=album-finish" enctype="multipart/form-data">
				<xsl:element name="input">
					<xsl:attribute name="type">hidden</xsl:attribute>
					<xsl:attribute name="name">id</xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="@id" /></xsl:attribute>
				</xsl:element>
				<table>
					<tbody>
						<xsl:call-template name="item" />

						<tr>
							<td>Začátek:</td>
							<td>
								<xsl:call-template name="input">
									<xsl:with-param name="type">date</xsl:with-param>
									<xsl:with-param name="name">start</xsl:with-param>
									<xsl:with-param name="class">date</xsl:with-param>
								</xsl:call-template>
							</td>
						</tr>

						<tr>
							<td>Konec:</td>
							<td>
								<xsl:call-template name="input">
									<xsl:with-param name="type">date</xsl:with-param>
									<xsl:with-param name="name">end</xsl:with-param>
									<xsl:with-param name="class">date</xsl:with-param>
								</xsl:call-template>
							</td>
						</tr>

						<tr>
							<td>Hlavní fotka:</td>
							<td>
								<xsl:call-template name="input">
									<xsl:with-param name="name">photo</xsl:with-param>
									<xsl:with-param name="id">photo</xsl:with-param>
								</xsl:call-template>
							</td>
						</tr>

						<tr>
							<td></td>
							<td><input type="submit" value="Uložit" /></td>
						</tr>

					</tbody>
				</table>
			</form>

			<xsl:if test="$id_album != 0">
			<h2>Kapitoly</h2>
				<xsl:variable name="newlink">
					<xsl:text>./?action=chapter&amp;id=0&amp;id_album=</xsl:text>
					<xsl:value-of select="@id" />
				</xsl:variable>
				<p>
					<a href="{$newlink}"><img src="/img/admin/add.png" title="Nová kapitola" /></a>
					<xsl:text>&#x2009;</xsl:text>
					<a href="{$newlink}">Nová kapitola</a>
				</p>
				<table>
					<tbody>
						<xsl:for-each select="chapters/chapter">
							<xsl:element name="tr">
								<xsl:if test="@visible = '0'">
									<xsl:attribute name="class">inactive</xsl:attribute>
								</xsl:if>
								<td>
									<xsl:element name="a">
										<xsl:attribute name="href">
											<xsl:text>./?action=chapter&amp;id=</xsl:text>
											<xsl:value-of select="@id" />
										</xsl:attribute>
										<xsl:value-of select="@name" />
									</xsl:element>
								</td>
								<td>
									<xsl:if test="position() != 1">
										<xsl:element name="a">
											<xsl:attribute name="href">
												<xsl:text>./?action=up&amp;id=</xsl:text>
												<xsl:value-of select="@id" />
												<xsl:text>&amp;id_album=</xsl:text>
												<xsl:value-of select="$id_album" />
											</xsl:attribute>
											<img src="/img/admin/up.png" title="Posunout nahoru" />
										</xsl:element>
									</xsl:if>
								</td>
								<td>
									<xsl:if test="position() != last()">
										<xsl:element name="a">
											<xsl:attribute name="href">
												<xsl:text>./?action=down&amp;id=</xsl:text>
												<xsl:value-of select="@id" />
												<xsl:text>&amp;id_album=</xsl:text>
												<xsl:value-of select="$id_album" />
											</xsl:attribute>
											<img src="/img/admin/down.png" title="Posunout dolů" />
										</xsl:element>
									</xsl:if>
								</td>
								<td>
									<xsl:element name="a">
										<xsl:attribute name="href">
											<xsl:text>./?action=chapter-delete&amp;id=</xsl:text>
											<xsl:value-of select="@id" />
											<xsl:text>&amp;id_album=</xsl:text>
											<xsl:value-of select="$id_album" />
										</xsl:attribute>
										<xsl:attribute name="class">confirm</xsl:attribute>
										<img src="/img/admin/delete.png" title="Smazat kapitolu" />
									</xsl:element>
								</td>
							</xsl:element>
						</xsl:for-each>
					</tbody>
				</table>
			</xsl:if>
		</body>
	</html>
	</xsl:template>

</xsl:stylesheet>
