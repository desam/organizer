<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2"
	xmlns:xql="http://metalab.unc.edu/xql/"
	xmlns:xq="http://namespaces.softwareag.com/tamino/XQuery/result"
	xmlns:exist="http://foo">
<xsl:output method="html" encoding="utf-8" indent="yes"/> 
	<xsl:template match='/'>
	<select>
		<xsl:apply-templates select='//categorie' />
	</select>
	</xsl:template>

	<xsl:template match ='categorie'>
	<xsl:variable name="IDCAT" select="@idc"/>
	<option value="$IDCAT"><xsl:value-of select='description' /></option>
	</xsl:template>
</xsl:stylesheet>