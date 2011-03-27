<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2"
	xmlns:xql="http://metalab.unc.edu/xql/"
	xmlns:xq="http://namespaces.softwareag.com/tamino/XQuery/result"
	xmlns:exist="http://foo">
<xsl:output method="html" encoding="utf-8" indent="yes"/> 

	<xsl:template match='/'>
	<HTML>
		<BODY>
			<H1>La liste des articles</H1>
			<TABLE Border='1'>
				<xsl:apply-templates select='//article' />
			</TABLE>
		</BODY>
	</HTML>
	</xsl:template>

	<xsl:template match ='article'>
		<xsl:variable name="IDA" select="@id"/>
		<TR>
			<TD><xsl:value-of select='$IDA'/> <B><xsl:value-of select='titre' /> </B>
			<xsl:value-of select='datepublication' /> 
			<span id="idarticle"><xsl:value-of select='$IDA'/></span>
            Catégorie[<xsl:value-of select='@refc' />]
			<a href="index.php?idarticle="> Suivant</a>	
		</TD>
		</TR>
		<TR>
			<TD><xsl:value-of select='description' /></TD>
		</TR>
		<TR></TR>
	</xsl:template>
	
</xsl:stylesheet>