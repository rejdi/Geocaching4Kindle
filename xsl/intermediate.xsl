<?xml version="1.0" encoding='utf8'?>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fn="http://www.w3.org/2005/xpath-functions"
	xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func fn">
<xsl:output method="xml" version="1.0" encoding="utf8" indent="yes"/>
<xsl:template match="/">
<cache>
<code><xsl:value-of select="normalize-space(//div[@class='HalfRight AlignRight']/h1)"/></code>
<location><xsl:value-of select="//p[@class='LatLong Meta']"/></location>
<locationUTM><xsl:value-of select="//p[@class='UTM Meta']"/></locationUTM>
<name><xsl:value-of select="//div[@id='Content']/h2"/></name>
<type>
<img><xsl:value-of select="//div[@id='Content']/h2/img/@src"/></img>
<text><xsl:value-of select="//div[@id='Content']/h2/img/@alt"/></text>
</type>
<link>http://www.geocaching.com/seek/cdpf.aspx?guid=<xsl:value-of select="substring-before(substring-after(//form[@name='Form1' and @id='Form1']/@action, 'guid='), '.html')"/></link>
<date><xsl:value-of select="substring-after(//p[@class='Meta' and substring(normalize-space(text()),1,10)='Placed Date: '], 'Placed Date: ')"/></date>
<author><xsl:value-of select="substring-after(//p[@class='Meta' and substring(normalize-space(text()),1,10)='Placed by:'], 'Placed by:')"/></author>

<!-- WTF je hidden? //-->
<hidden><xsl:value-of select="substring-after(normalize-space(//span[@class='minorCacheDetails' and substring(normalize-space(text()),1,8)='Hidden :']),'Hidden : ')"/></hidden>

<difficulty>
	<xsl:value-of select="substring-before(normalize-space(//div[@class='Third AlignCenter']/p[@class='Meta']/img/@alt),' out of')"/>
</difficulty>

<terrain>
	<xsl:value-of select="substring-before(normalize-space(//div[@class='Third AlignRight']/p[@class='Meta']/img/@alt),' out of')"/>
</terrain>

<size>
	<xsl:value-of select="substring-before(normalize-space(//div[@class='Third']/p[@class='Meta']/img/@alt),' out of')"/>
	<xsl:value-of select="substring-after(substring-before(//div[@class='Third']/p[@class='Meta']/img[substring-before(@alt,':')='Size']/../small,')'),'(')"/>
</size>

<hintEncrypted><xsl:value-of select="//div[@id='uxDecryptedHint']"/></hintEncrypted>
<hintDecrypted><xsl:value-of select="//div[@id='uxEncryptedHint']"/></hintDecrypted>
<hintKey><xsl:value-of select="//span[@class='EncryptionKey HalfRight']/blockquote"/></hintKey>

<short-description>
	<xsl:copy-of select="//div[@class='item']//h2[contains(., 'Short')]/../../div[@class='item-content']"/>
</short-description>

<long-description>
	<xsl:copy-of select="//div[@class='item']//h2[contains(., 'Long')]/../../div[@class='item-content']"/>
</long-description>


<xsl:for-each select="//div[@class='item']//h2[contains(., 'Logs')]/../../div[@class='item-content']/dl/dt">
<log>
	<type>
		<xsl:value-of select="img/@alt"/>
	</type>
	<user>
		<xsl:value-of select="strong"/>
	</user>
	<comment>
		<xsl:variable name="pos" select="position()"/>
		<xsl:copy-of select="../dd[$pos]"/>
	</comment>
	<time>
		<xsl:value-of select="substring-before(normalize-space(substring-after(., ']')), ' by ')"/>
	</time>
</log>
</xsl:for-each>

<xsl:for-each select="//table[@id='Waypoints']/tbody/tr[@ishidden='false']">
<waypoint>
	<prefix><xsl:value-of select="td[4]"/></prefix>
	<lookup><xsl:value-of select="td[5]"/></lookup>
	<name><xsl:value-of select="td[6]"/></name>
	<position><xsl:value-of select="td[7]"/></position>
	<note>
		<xsl:variable name="pos" select="position()"/>
		<xsl:value-of select="../tr[not(@ishidden='false')][$pos]/td[3]"/>
	</note>
</waypoint>
</xsl:for-each>

<meta>
	<xsl:value-of select="//div[@id='Content']/p[@class='Meta']"/>
</meta>

<attributes>
	<xsl:value-of select="substring-after(//div[@class='item']//h2[contains(., 'Attributes')]/../../div[@class='item-content'], 'What are Attributes?')"/>
</attributes>

<!-- Toto bude musiet byt spravene cez JS a kindlegen si s tym bude musiet poradit. -> cize naincludovat skripty//-->
<map>
	<xsl:variable name="details" select="normalize-space(substring-before(substring-after(//script[contains(., 'lat=')], 'CDATA['), '//]]'))"/>
	<xsl:value-of select="$details"/>
	<lat><xsl:value-of select="substring-before(substring-after($details, 'lat='), ',')"/></lat>
	<lng><xsl:value-of select="substring-before(substring-after($details, 'lng='), ',')"/></lng>
	<wptid><xsl:value-of select="substring-before(substring-after($details, 'wptid='), ',')"/></wptid>
</map>

<!-- wtf? //-->
<warning>
	<xsl:for-each select='//p[@class="OldWarning NoBottomSpacing"]/strong'>
	<h2><xsl:value-of select="."/></h2>
	</xsl:for-each>
	<xsl:copy-of select='//ul[@class="OldWarning"]'/>
</warning>

</cache>


</xsl:template>


</xsl:stylesheet>
