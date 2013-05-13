<?xml version="1.0" encoding='utf-8'?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:fn="http://www.w3.org/2005/xpath-functions"
	xmlns:func="http://exslt.org/functions"
	xmlns:math="http://exslt.org/math"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="func fn">
<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes"/>
<xsl:template match='/'>
<!-- <gpx creator="Rejdi" version="1.1" xmlns="http://www.topografix.com/GPX/1/1" xmlns:groundspeak="http://www.groundspeak.com/cache/1/1"> -->
<gpx xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" version="1.0" creator="Geocaching generator" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0 http://www.groundspeak.com/cache/1/0/cache.xsd" xmlns="http://www.topografix.com/GPX/1/0" xmlns:groundspeak="http://www.groundspeak.com/cache/1/0">
<bounds minlat="{math:min(//lat)}" minlon="{math:min(//lon)}" maxlat="{math:max(//lat)}" maxlon="{math:max(//lon)}"/>
<xsl:for-each select="//cache">
<wpt>

<xsl:attribute name="lat">
<xsl:value-of select='map/lat'/>
</xsl:attribute>

<xsl:attribute name='lon'>
<xsl:value-of select='map/lon'/>
</xsl:attribute>
<time><xsl:value-of select="php:function('normalizeTime', normalize-space(date))"/></time>
<name><xsl:value-of select="code"/></name>
<desc><xsl:value-of select="normalize-space(name)"/> by <xsl:value-of select="normalize-space(author)"/>, <xsl:value-of select="normalize-space(type/text)"/> (<xsl:value-of select="normalize-space(difficulty)"/>/<xsl:value-of select="normalize-space(terrain)"/>)</desc>

<url>
<xsl:value-of select="normalize-space(link)"/>
</url>

<urlname>
<xsl:value-of select="normalize-space(name)"/>
</urlname>

<sym>Geocache</sym>
<type>Geocache|<xsl:value-of select="normalize-space(type/text)"/></type>

<groundspeak:cache available="True" archived="False">
	<groundspeak:name>
		<xsl:value-of select="normalize-space(name)"/>
	</groundspeak:name>
	<groundspeak:placed_by>
		<xsl:value-of select="normalize-space(author)"/>
	</groundspeak:placed_by>
	<groundspeak:type>
		<xsl:value-of select="normalize-space(type/text)"/>
	</groundspeak:type>
	<groundspeak:container>
		<xsl:value-of select="normalize-space(size/text)"/>
	</groundspeak:container>
	<groundspeak:difficulty>
		<xsl:value-of select="normalize-space(difficulty)"/>
	</groundspeak:difficulty>
	<groundspeak:terrain>
		<xsl:value-of select="normalize-space(terrain)"/>
	</groundspeak:terrain>
	<groundspeak:short_description html="True">
		<xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text>
			<xsl:copy-of select="short-description/*"/>
		<xsl:text disable-output-escaping="yes">]]&gt;</xsl:text>
	</groundspeak:short_description>
	<groundspeak:long_description html="True">
		<xsl:text disable-output-escaping="yes">&lt;![CDATA[</xsl:text>
			<xsl:copy-of select="long-description/*"/>
			
			<xsl:if test="waypoint">
			<p>Additional waypoints:
			<xsl:for-each select="waypoint">
				<br/>
				<xsl:value-of select="normalize-space(name)"/>
				(<xsl:value-of select="normalize-space(lookup)"/>):
				<xsl:value-of select="normalize-space(note)"/>
			</xsl:for-each>
			</p>
			</xsl:if>
			
		<xsl:text disable-output-escaping="yes">]]&gt;</xsl:text>
	</groundspeak:long_description>
	<groundspeak:encoded_hints>
		<xsl:value-of select="normalize-space(hintDecrypted)"/>
	</groundspeak:encoded_hints>
	
	<groundspeak:logs>
		<xsl:for-each select="log">
		<groundspeak:log>
			<groundspeak:date>
				<xsl:value-of select="php:function('normalizeTime', normalize-space(time))"/>
			</groundspeak:date>
			<groundspeak:type>
				<xsl:value-of select="normalize-space(type)"/>
			</groundspeak:type>
			<groundspeak:finder>
				<xsl:value-of select="normalize-space(user)"/>
			</groundspeak:finder>
			<groundspeak:text>
				<xsl:value-of select="normalize-space(comment)"/>
			</groundspeak:text>
		</groundspeak:log>
		</xsl:for-each>
	</groundspeak:logs>

</groundspeak:cache>

</wpt>

<xsl:if test="count(waypoint/lat) &gt; 0">
<rte>
	<name><xsl:value-of select="normalize-space(name)"/></name>
	<xsl:for-each select="waypoint[lat]">
		<rtept lat="{lat}" lon="{lon}">
			<name><xsl:value-of select="normalize-space(name)"/></name>
		</rtept>
	</xsl:for-each>
</rte>
</xsl:if>

</xsl:for-each>

</gpx>
</xsl:template>
</xsl:stylesheet>
