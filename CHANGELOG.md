# Release Notes
In order to standardize and improve our release process, the Ilios team has discontinued the use of this change log
and will instead publish changes in our [Release Notes](https://github.com/ilios/ilios/releases)

# Change Log (for previous releases)

## [v3.28.1](https://github.com/ilios/ilios/tree/v3.28.1)

[Full Changelog](https://github.com/ilios/ilios/compare/v3.28.0...v3.28.1)

**Closed issues:**

- Users can escalate their own permissions [\#1762](https://github.com/ilios/ilios/issues/1762)

**Merged pull requests:**

- Prevent users from editing their own account [\#1764](https://github.com/ilios/ilios/pull/1764) ([jrjohnson](https://github.com/jrjohnson))

## [v3.28.0](https://github.com/ilios/ilios/tree/v3.28.0) (2017-02-24)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.27.0...v3.28.0)

**Implemented enhancements:**

- sortable course/session learning materials [\#1729](https://github.com/ilios/ilios/issues/1729)
- Don't fail to run tests when a matching frontend can not be found [\#1626](https://github.com/ilios/ilios/issues/1626)

**Closed issues:**

- Should non-root users be able to modify root users? [\#1757](https://github.com/ilios/ilios/issues/1757)

**Merged pull requests:**

- updating changelog for v3.28.0 release [\#1761](https://github.com/ilios/ilios/pull/1761) ([thecoolestguy](https://github.com/thecoolestguy))
- Updated PHP requirement and added link to IUS repo [\#1760](https://github.com/ilios/ilios/pull/1760) ([thecoolestguy](https://github.com/thecoolestguy))
- consider is-root attribute in user voter [\#1758](https://github.com/ilios/ilios/pull/1758) ([stopfstedt](https://github.com/stopfstedt))
- Fix managers that should be DTOs [\#1756](https://github.com/ilios/ilios/pull/1756) ([jrjohnson](https://github.com/jrjohnson))
- Fail gracefully on missing frontend [\#1755](https://github.com/ilios/ilios/pull/1755) ([jrjohnson](https://github.com/jrjohnson))
- sortable course/session learning materials [\#1754](https://github.com/ilios/ilios/pull/1754) ([stopfstedt](https://github.com/stopfstedt))
- Update Dependencies [\#1752](https://github.com/ilios/ilios/pull/1752) ([jrjohnson](https://github.com/jrjohnson))

## [v3.27.0](https://github.com/ilios/ilios/tree/v3.27.0) (2017-02-10)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.26.0...v3.27.0)

**Implemented enhancements:**

- Align docker-composer.yml with new Ilios images [\#1747](https://github.com/ilios/ilios/issues/1747)
- Handle service worker script tag in frontend JSON file [\#1692](https://github.com/ilios/ilios/issues/1692)

**Closed issues:**

- add application and school configuration to schema [\#1735](https://github.com/ilios/ilios/issues/1735)

**Merged pull requests:**

- updating the CHANGELOG for the v3.27.0 release [\#1750](https://github.com/ilios/ilios/pull/1750) ([thecoolestguy](https://github.com/thecoolestguy))
- Better Ilios Development Setup [\#1748](https://github.com/ilios/ilios/pull/1748) ([jrjohnson](https://github.com/jrjohnson))
- Create ancestor relationship when rolling over courses [\#1745](https://github.com/ilios/ilios/pull/1745) ([jrjohnson](https://github.com/jrjohnson))
- added missing word 'create' [\#1744](https://github.com/ilios/ilios/pull/1744) ([thecoolestguy](https://github.com/thecoolestguy))
- Remove debug parameters which are only used in dev [\#1741](https://github.com/ilios/ilios/pull/1741) ([jrjohnson](https://github.com/jrjohnson))
- Allow ILIOS\_\* env variables to fill parameters.yml [\#1740](https://github.com/ilios/ilios/pull/1740) ([jrjohnson](https://github.com/jrjohnson))
- application and school configuration [\#1736](https://github.com/ilios/ilios/pull/1736) ([stopfstedt](https://github.com/stopfstedt))

## [v3.26.0](https://github.com/ilios/ilios/tree/v3.26.0) (2017-02-04)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.25.0...v3.26.0)

**Closed issues:**

- include terms linked to owned sessions in terms filter to course endpoint.  [\#1737](https://github.com/ilios/ilios/issues/1737)
- Add 'composer install' step to vagrant build [\#789](https://github.com/ilios/ilios/issues/789)

**Merged pull requests:**

- updating changelog for the v3.26.0 release [\#1739](https://github.com/ilios/ilios/pull/1739) ([thecoolestguy](https://github.com/thecoolestguy))
- include session terms in course filter [\#1738](https://github.com/ilios/ilios/pull/1738) ([stopfstedt](https://github.com/stopfstedt))
- Replace Vagrant with docker [\#1607](https://github.com/ilios/ilios/pull/1607) ([jrjohnson](https://github.com/jrjohnson))

## [v3.25.0](https://github.com/ilios/ilios/tree/v3.25.0) (2017-01-27)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.24.1...v3.25.0)

**Implemented enhancements:**

- Add term filters to API endpoints for reporting [\#1725](https://github.com/ilios/ilios/issues/1725)
- Add reporting filters to Term endpoint [\#1724](https://github.com/ilios/ilios/issues/1724)

**Closed issues:**

- Permissions on departments are too restrictive [\#1722](https://github.com/ilios/ilios/issues/1722)

**Merged pull requests:**

- updating changelog for 3.25.0 release [\#1733](https://github.com/ilios/ilios/pull/1733) ([thecoolestguy](https://github.com/thecoolestguy))
- Update the API version we provide [\#1732](https://github.com/ilios/ilios/pull/1732) ([jrjohnson](https://github.com/jrjohnson))
- filter terms by program years. [\#1731](https://github.com/ilios/ilios/pull/1731) ([stopfstedt](https://github.com/stopfstedt))
- implemented filter by terms. [\#1728](https://github.com/ilios/ilios/pull/1728) ([stopfstedt](https://github.com/stopfstedt))
- fixed fixture data. [\#1727](https://github.com/ilios/ilios/pull/1727) ([stopfstedt](https://github.com/stopfstedt))
- filter session types by terms [\#1726](https://github.com/ilios/ilios/pull/1726) ([stopfstedt](https://github.com/stopfstedt))
- Grant VIEW permissions for all departments [\#1723](https://github.com/ilios/ilios/pull/1723) ([jrjohnson](https://github.com/jrjohnson))

## [v3.24.1](https://github.com/ilios/ilios/tree/v3.24.1) (2017-01-13)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.24.0...v3.24.1)

**Closed issues:**

- Start testing in PHP 7.1 [\#1719](https://github.com/ilios/ilios/issues/1719)
- Some tests which relied on static offering are broken [\#1716](https://github.com/ilios/ilios/issues/1716)
- Sudden ICS feed test failures [\#1714](https://github.com/ilios/ilios/issues/1714)
- Unable to rollover course into current academic year [\#1711](https://github.com/ilios/ilios/issues/1711)
- Maximum TTL not being applied for tokens [\#1709](https://github.com/ilios/ilios/issues/1709)

**Merged pull requests:**

- updating changelog for the v3.24.1 release [\#1721](https://github.com/ilios/ilios/pull/1721) ([thecoolestguy](https://github.com/thecoolestguy))
- added php 7.1 to test matrix [\#1720](https://github.com/ilios/ilios/pull/1720) ([stopfstedt](https://github.com/stopfstedt))
- Use variable date from offering fixture in tests [\#1717](https://github.com/ilios/ilios/pull/1717) ([jrjohnson](https://github.com/jrjohnson))
- Use a relative timestamp for some test offering data [\#1715](https://github.com/ilios/ilios/pull/1715) ([jrjohnson](https://github.com/jrjohnson))
- Allow courses to be rolled over in the previous year [\#1713](https://github.com/ilios/ilios/pull/1713) ([jrjohnson](https://github.com/jrjohnson))
- Update dependencies [\#1712](https://github.com/ilios/ilios/pull/1712) ([jrjohnson](https://github.com/jrjohnson))
- Correctly apply maximum TTL for tokens [\#1710](https://github.com/ilios/ilios/pull/1710) ([jrjohnson](https://github.com/jrjohnson))

## [v3.24.0](https://github.com/ilios/ilios/tree/v3.24.0) (2016-12-22)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.23.0...v3.24.0)

**Implemented enhancements:**

- Pull the container out of TrackApiUsageListener [\#1691](https://github.com/ilios/ilios/issues/1691)

**Closed issues:**

- Add apcu to ilios requirements [\#1703](https://github.com/ilios/ilios/issues/1703)
- course rollover tests broken [\#1700](https://github.com/ilios/ilios/issues/1700)
- fix deprecation warnings [\#1699](https://github.com/ilios/ilios/issues/1699)
- rename mesh\_tree\_x\_descriptor [\#1696](https://github.com/ilios/ilios/issues/1696)
- Audit Log valuesChanged Field Not Updating Correctly [\#1694](https://github.com/ilios/ilios/issues/1694)
- Errors reporting to google should not cause request to fail [\#1689](https://github.com/ilios/ilios/issues/1689)

**Merged pull requests:**

- Read content from scripts when pulling frontend JSON [\#1742](https://github.com/ilios/ilios/pull/1742) ([jrjohnson](https://github.com/jrjohnson))
- updating changelog for the v3.24.0 release [\#1708](https://github.com/ilios/ilios/pull/1708) ([thecoolestguy](https://github.com/thecoolestguy))
- moving url rewriting instructions into its own section. [\#1705](https://github.com/ilios/ilios/pull/1705) ([stopfstedt](https://github.com/stopfstedt))
- Composer require apcu [\#1704](https://github.com/ilios/ilios/pull/1704) ([stopfstedt](https://github.com/stopfstedt))
- another attempt to make tests pass if we're rolling over year boundaries. [\#1702](https://github.com/ilios/ilios/pull/1702) ([stopfstedt](https://github.com/stopfstedt))
- fix deprecation warnings [\#1701](https://github.com/ilios/ilios/pull/1701) ([stopfstedt](https://github.com/stopfstedt))
- Convert Entities to DTO pattern [\#1698](https://github.com/ilios/ilios/pull/1698) ([jrjohnson](https://github.com/jrjohnson))
- schema change: replaced mesh\_tree\_x\_descriptor with mesh\_tree table. [\#1697](https://github.com/ilios/ilios/pull/1697) ([stopfstedt](https://github.com/stopfstedt))
- Log relationship changes for entities [\#1695](https://github.com/ilios/ilios/pull/1695) ([jrjohnson](https://github.com/jrjohnson))
- refactored track api usage listener w/o container aware trait. [\#1693](https://github.com/ilios/ilios/pull/1693) ([stopfstedt](https://github.com/stopfstedt))
- catch and log tracking exceptions. [\#1690](https://github.com/ilios/ilios/pull/1690) ([stopfstedt](https://github.com/stopfstedt))

## [v3.23.0](https://github.com/ilios/ilios/tree/v3.23.0) (2016-12-02)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.22.0...v3.23.0)

**Implemented enhancements:**

- Identify all faculty in a given school [\#1670](https://github.com/ilios/ilios/issues/1670)
- log API usage [\#1148](https://github.com/ilios/ilios/issues/1148)

**Closed issues:**

- Unquoted parameters deprecation [\#1685](https://github.com/ilios/ilios/issues/1685)
- Deadlock errors when creating multiple offerings [\#1683](https://github.com/ilios/ilios/issues/1683)

**Merged pull requests:**

- updating changelog for the v3.23.0 release [\#1688](https://github.com/ilios/ilios/pull/1688) ([thecoolestguy](https://github.com/thecoolestguy))
- Updated Dependencies [\#1687](https://github.com/ilios/ilios/pull/1687) ([jrjohnson](https://github.com/jrjohnson))
- Quote configuration parameters [\#1686](https://github.com/ilios/ilios/pull/1686) ([jrjohnson](https://github.com/jrjohnson))
- Timestamp updated entities outside of the normal flow [\#1684](https://github.com/ilios/ilios/pull/1684) ([jrjohnson](https://github.com/jrjohnson))
- added requirement notes for php-zip extension and URL-rewriting [\#1681](https://github.com/ilios/ilios/pull/1681) ([thecoolestguy](https://github.com/thecoolestguy))
- track API usage [\#1677](https://github.com/ilios/ilios/pull/1677) ([stopfstedt](https://github.com/stopfstedt))

## [v3.22.0](https://github.com/ilios/ilios/tree/v3.22.0) (2016-11-18)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.21.0...v3.22.0)

**Implemented enhancements:**

- Add courseId to session user materials [\#1672](https://github.com/ilios/ilios/issues/1672)

**Closed issues:**

- Add firstOfferingDate \(from course start data\) to usermaterials [\#1676](https://github.com/ilios/ilios/issues/1676)
- Filter objectives by relationship [\#1666](https://github.com/ilios/ilios/issues/1666)
- Filter /usermaterials by date [\#1661](https://github.com/ilios/ilios/issues/1661)

**Merged pull requests:**

- updating changelog for v3.22.0 release [\#1680](https://github.com/ilios/ilios/pull/1680) ([thecoolestguy](https://github.com/thecoolestguy))
- Add firstOfferingDate to course learning materials [\#1679](https://github.com/ilios/ilios/pull/1679) ([jrjohnson](https://github.com/jrjohnson))
- Use DTO pattern for competencies [\#1678](https://github.com/ilios/ilios/pull/1678) ([jrjohnson](https://github.com/jrjohnson))
- Add courseId and title to session usermaterials [\#1673](https://github.com/ilios/ilios/pull/1673) ([jrjohnson](https://github.com/jrjohnson))
- added documentation for using the JSON Web Tokens [\#1671](https://github.com/ilios/ilios/pull/1671) ([thecoolestguy](https://github.com/thecoolestguy))
- Filter Usermaterials by date [\#1668](https://github.com/ilios/ilios/pull/1668) ([jrjohnson](https://github.com/jrjohnson))
- Make objective a DTO [\#1667](https://github.com/ilios/ilios/pull/1667) ([jrjohnson](https://github.com/jrjohnson))
- added frontend-update command instructions [\#1665](https://github.com/ilios/ilios/pull/1665) ([thecoolestguy](https://github.com/thecoolestguy))
- updated version requirements in INSTALL.md [\#1662](https://github.com/ilios/ilios/pull/1662) ([thecoolestguy](https://github.com/thecoolestguy))

## [v3.21.0](https://github.com/ilios/ilios/tree/v3.21.0) (2016-10-28)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.20.0...v3.21.0)

**Implemented enhancements:**

- Put API version into /application/config [\#1655](https://github.com/ilios/ilios/issues/1655)
- Advertise maximum upload size at /application/config [\#1648](https://github.com/ilios/ilios/issues/1648)
- Add learnerSessions filter to users endpoint [\#1643](https://github.com/ilios/ilios/issues/1643)
- Add /usermaterials/ endpoint [\#1635](https://github.com/ilios/ilios/issues/1635)
- create cohort when program year is created [\#1633](https://github.com/ilios/ilios/issues/1633)
- Add directors relationship to program [\#1631](https://github.com/ilios/ilios/issues/1631)
- Add administrator relationship to session [\#1624](https://github.com/ilios/ilios/issues/1624)
- Add administrators relationship to course [\#1621](https://github.com/ilios/ilios/issues/1621)
- Add administrators relationship to school [\#1618](https://github.com/ilios/ilios/issues/1618)
- Add directors relationship to school [\#1617](https://github.com/ilios/ilios/issues/1617)
- add ability to clone curriculum inventory reports [\#1614](https://github.com/ilios/ilios/issues/1614)
- Update PCRS Mapping [\#1456](https://github.com/ilios/ilios/issues/1456)

**Closed issues:**

- Draft Learning Materials Showing Up in Calendar Feed [\#1659](https://github.com/ilios/ilios/issues/1659)
- curriculum inventory report controller fails to rollover finalized reports [\#1653](https://github.com/ilios/ilios/issues/1653)
- realign URL for curriculum inventory reports rollover. [\#1650](https://github.com/ilios/ilios/issues/1650)
- Name conflict in JsonWebTokenAuthenticator [\#1645](https://github.com/ilios/ilios/issues/1645)
- PHPCS not running against /tests [\#1636](https://github.com/ilios/ilios/issues/1636)
- Add "is root" attribute to users [\#1623](https://github.com/ilios/ilios/issues/1623)
- Be more frugal about updating relationships [\#1606](https://github.com/ilios/ilios/issues/1606)
- When Course objectives are deleted, cascade to delete session obj \> course obj. relationship [\#1597](https://github.com/ilios/ilios/issues/1597)
- update vagrant image to ubuntu 16.04 [\#1554](https://github.com/ilios/ilios/issues/1554)
- API documentation for Session PUT is confusing [\#1468](https://github.com/ilios/ilios/issues/1468)
- Doctrine Migrations fail in MySQL 5.6+ [\#1449](https://github.com/ilios/ilios/issues/1449)
- Upgrade to Symfony 3 [\#1417](https://github.com/ilios/ilios/issues/1417)
- Load test bulk group management in the API [\#1378](https://github.com/ilios/ilios/issues/1378)
- Fail better on large file uploads [\#1196](https://github.com/ilios/ilios/issues/1196)
- Use the symfony PHPUnit bridge [\#1077](https://github.com/ilios/ilios/issues/1077)

**Merged pull requests:**

- Exclude draft LMs from ICS Feed [\#1660](https://github.com/ilios/ilios/pull/1660) ([jrjohnson](https://github.com/jrjohnson))
- Add learnerSessions filter to users [\#1658](https://github.com/ilios/ilios/pull/1658) ([jrjohnson](https://github.com/jrjohnson))
- Add API version to configuration [\#1657](https://github.com/ilios/ilios/pull/1657) ([jrjohnson](https://github.com/jrjohnson))
- Create cohort with program year [\#1656](https://github.com/ilios/ilios/pull/1656) ([stopfstedt](https://github.com/stopfstedt))
- Use Symfony builtin to detect maximum file upload size [\#1654](https://github.com/ilios/ilios/pull/1654) ([jrjohnson](https://github.com/jrjohnson))
- enforce create perms check on rollover request. [\#1652](https://github.com/ilios/ilios/pull/1652) ([stopfstedt](https://github.com/stopfstedt))
- fixed route to ci rollover report endpoint. [\#1651](https://github.com/ilios/ilios/pull/1651) ([stopfstedt](https://github.com/stopfstedt))
- Add maxUploadSize key to config [\#1649](https://github.com/ilios/ilios/pull/1649) ([jrjohnson](https://github.com/jrjohnson))
- Clarify error message for large file uploads [\#1647](https://github.com/ilios/ilios/pull/1647) ([jrjohnson](https://github.com/jrjohnson))
- Use AuthenticationInterface as a different name to avoid conflicts [\#1646](https://github.com/ilios/ilios/pull/1646) ([jrjohnson](https://github.com/jrjohnson))
- Add endpoint to get all materials for a user [\#1644](https://github.com/ilios/ilios/pull/1644) ([jrjohnson](https://github.com/jrjohnson))
- fixed/cleaned up some regexp in CLI command tests. [\#1641](https://github.com/ilios/ilios/pull/1641) ([stopfstedt](https://github.com/stopfstedt))
- prefixed Ilios namespace with slash. [\#1640](https://github.com/ilios/ilios/pull/1640) ([stopfstedt](https://github.com/stopfstedt))
- updated PHPUnit to version 5.6 [\#1639](https://github.com/ilios/ilios/pull/1639) ([stopfstedt](https://github.com/stopfstedt))
- Update Libraries [\#1638](https://github.com/ilios/ilios/pull/1638) ([jrjohnson](https://github.com/jrjohnson))
- runs PHPCS against /tests dir [\#1637](https://github.com/ilios/ilios/pull/1637) ([stopfstedt](https://github.com/stopfstedt))
- When a course objective is removed unlink session objectives [\#1634](https://github.com/ilios/ilios/pull/1634) ([jrjohnson](https://github.com/jrjohnson))
- Add director to program [\#1632](https://github.com/ilios/ilios/pull/1632) ([jrjohnson](https://github.com/jrjohnson))
- added "root" attribute to user entity [\#1630](https://github.com/ilios/ilios/pull/1630) ([stopfstedt](https://github.com/stopfstedt))
- Add administrator relationship to school [\#1629](https://github.com/ilios/ilios/pull/1629) ([jrjohnson](https://github.com/jrjohnson))
- Add administrators relationship to course [\#1628](https://github.com/ilios/ilios/pull/1628) ([jrjohnson](https://github.com/jrjohnson))
- Add administrator relationship to session [\#1627](https://github.com/ilios/ilios/pull/1627) ([jrjohnson](https://github.com/jrjohnson))
- Add directors relationship to school [\#1625](https://github.com/ilios/ilios/pull/1625) ([jrjohnson](https://github.com/jrjohnson))
- curriculum inventory report rollover. [\#1616](https://github.com/ilios/ilios/pull/1616) ([stopfstedt](https://github.com/stopfstedt))
- Improved Front Controller [\#1615](https://github.com/ilios/ilios/pull/1615) ([jrjohnson](https://github.com/jrjohnson))
- set a cookie to report download response. [\#1613](https://github.com/ilios/ilios/pull/1613) ([stopfstedt](https://github.com/stopfstedt))
- Update relationships only when needed [\#1611](https://github.com/ilios/ilios/pull/1611) ([jrjohnson](https://github.com/jrjohnson))

## [v3.20.0](https://github.com/ilios/ilios/tree/v3.20.0) (2016-09-30)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.19.0...v3.20.0)

**Implemented enhancements:**

- Increase limit for external id on course [\#1598](https://github.com/ilios/ilios/issues/1598)
- Cascade deletes for learner groups [\#1594](https://github.com/ilios/ilios/issues/1594)

**Closed issues:**

- Reorganize timestamp updates [\#1605](https://github.com/ilios/ilios/issues/1605)
- default login fails with internal server error on invalid credentials [\#1603](https://github.com/ilios/ilios/issues/1603)
- 'UCSF'-specific reference in template body text should be removed [\#1596](https://github.com/ilios/ilios/issues/1596)
- Better log entry for missing EPPN [\#1471](https://github.com/ilios/ilios/issues/1471)

**Merged pull requests:**

- updating changelog for the v3.20.0 release [\#1609](https://github.com/ilios/ilios/pull/1609) ([thecoolestguy](https://github.com/thecoolestguy))
- Replace Listeners for time stamping [\#1608](https://github.com/ilios/ilios/pull/1608) ([jrjohnson](https://github.com/jrjohnson))
- issue correct HTTP response on authn failure [\#1604](https://github.com/ilios/ilios/pull/1604) ([stopfstedt](https://github.com/stopfstedt))
- Log less information when there is a missing ship parameter [\#1602](https://github.com/ilios/ilios/pull/1602) ([jrjohnson](https://github.com/jrjohnson))
- removed the line referencing 'UCSF' from the email template [\#1600](https://github.com/ilios/ilios/pull/1600) ([dartajax](https://github.com/dartajax))
- Expand Course externalId to 255 characters [\#1599](https://github.com/ilios/ilios/pull/1599) ([jrjohnson](https://github.com/jrjohnson))
- Cascade learner group deletes to sub groups [\#1595](https://github.com/ilios/ilios/pull/1595) ([jrjohnson](https://github.com/jrjohnson))

## [v3.19.0](https://github.com/ilios/ilios/tree/v3.19.0) (2016-09-16)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.18.1...v3.19.0)

**Implemented enhancements:**

- Authentication should be part of the user record [\#1589](https://github.com/ilios/ilios/issues/1589)
- Allow unlocking locked items [\#1582](https://github.com/ilios/ilios/issues/1582)

**Closed issues:**

- Allow Authentication to be saved with no username [\#1590](https://github.com/ilios/ilios/issues/1590)
- Curriculum Inventory collects all available academic levels, rather than those referenced in the report [\#1584](https://github.com/ilios/ilios/issues/1584)

**Merged pull requests:**

- Updating the CHANGELOG.md file v3.19.0 release [\#1593](https://github.com/ilios/ilios/pull/1593) ([thecoolestguy](https://github.com/thecoolestguy))
- Show authentication data in user response [\#1592](https://github.com/ilios/ilios/pull/1592) ([jrjohnson](https://github.com/jrjohnson))
- When nd empty username is sent store it as null [\#1591](https://github.com/ilios/ilios/pull/1591) ([jrjohnson](https://github.com/jrjohnson))
- Only include used levels in CI export [\#1587](https://github.com/ilios/ilios/pull/1587) ([jrjohnson](https://github.com/jrjohnson))
- Fix flakey end of the year tests [\#1586](https://github.com/ilios/ilios/pull/1586) ([jrjohnson](https://github.com/jrjohnson))
- Add ancestry to courses and objectives [\#1585](https://github.com/ilios/ilios/pull/1585) ([jrjohnson](https://github.com/jrjohnson))
- Allow lockable entities to be unlocked [\#1583](https://github.com/ilios/ilios/pull/1583) ([jrjohnson](https://github.com/jrjohnson))

## [v3.18.1](https://github.com/ilios/ilios/tree/v3.18.1) (2016-09-09)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.18.0...v3.18.1)

**Closed issues:**

- Delete Permissions not checked for Archivable and Lockable entities [\#1576](https://github.com/ilios/ilios/issues/1576)
- Base URL Is Being Cut From Learning Material URL's on Calendar Feed [\#1574](https://github.com/ilios/ilios/issues/1574)
- UserID is not being exported as part of the audit log  [\#1570](https://github.com/ilios/ilios/issues/1570)
- Tests fail for offering rollovers that cross the new year [\#1568](https://github.com/ilios/ilios/issues/1568)
- Call to a member function setSession\(\) on null [\#1564](https://github.com/ilios/ilios/issues/1564)
- Session destroy in shibboleth logout [\#1563](https://github.com/ilios/ilios/issues/1563)
- Correct typo in WebIndexFromJson.php [\#1561](https://github.com/ilios/ilios/issues/1561)

**Merged pull requests:**

- updating changelog for the v3.18.1 release [\#1578](https://github.com/ilios/ilios/pull/1578) ([thecoolestguy](https://github.com/thecoolestguy))
- Enforce permission for archivable and lockable entities [\#1577](https://github.com/ilios/ilios/pull/1577) ([jrjohnson](https://github.com/jrjohnson))
- Fix Learning Material absolute path [\#1575](https://github.com/ilios/ilios/pull/1575) ([jrjohnson](https://github.com/jrjohnson))
- Remove EXTRA\_LAZY annotations [\#1573](https://github.com/ilios/ilios/pull/1573) ([jrjohnson](https://github.com/jrjohnson))
- Add userId to exported audit log [\#1571](https://github.com/ilios/ilios/pull/1571) ([jrjohnson](https://github.com/jrjohnson))
- Adjust expected end week [\#1569](https://github.com/ilios/ilios/pull/1569) ([jrjohnson](https://github.com/jrjohnson))
- Correct typo for AWS\_BUCKET [\#1567](https://github.com/ilios/ilios/pull/1567) ([jrjohnson](https://github.com/jrjohnson))
- Don't set the session if we are unsetting the ILM session [\#1566](https://github.com/ilios/ilios/pull/1566) ([jrjohnson](https://github.com/jrjohnson))
- Detect if a session exists before attempting to destroy it [\#1565](https://github.com/ilios/ilios/pull/1565) ([jrjohnson](https://github.com/jrjohnson))

## [v3.18.0](https://github.com/ilios/ilios/tree/v3.18.0) (2016-09-02)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.17.0...v3.18.0)

**Implemented enhancements:**

- Add a /find path to the directory controller [\#1556](https://github.com/ilios/ilios/issues/1556)

**Closed issues:**

- Shibboleth config will not load [\#1557](https://github.com/ilios/ilios/issues/1557)
- Improve developer logging [\#1551](https://github.com/ilios/ilios/issues/1551)
- Improve composer instructions [\#1549](https://github.com/ilios/ilios/issues/1549)

**Merged pull requests:**

- updating CHANGELOG.md file for v3.18.0 release [\#1562](https://github.com/ilios/ilios/pull/1562) ([thecoolestguy](https://github.com/thecoolestguy))
- Bump the supplied API version to v1.9 [\#1560](https://github.com/ilios/ilios/pull/1560) ([jrjohnson](https://github.com/jrjohnson))
- Add find to directory lookup [\#1559](https://github.com/ilios/ilios/pull/1559) ([jrjohnson](https://github.com/jrjohnson))
- User Request param instead of a service [\#1558](https://github.com/ilios/ilios/pull/1558) ([jrjohnson](https://github.com/jrjohnson))
- Update Symfony to 3.1 [\#1553](https://github.com/ilios/ilios/pull/1553) ([jrjohnson](https://github.com/jrjohnson))
- Add EasyLog logger [\#1552](https://github.com/ilios/ilios/pull/1552) ([jrjohnson](https://github.com/jrjohnson))
- Improved composer install instructions [\#1550](https://github.com/ilios/ilios/pull/1550) ([jrjohnson](https://github.com/jrjohnson))

## [v3.17.0](https://github.com/ilios/ilios/tree/v3.17.0) (2016-08-17)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.16.0...v3.17.0)

**Implemented enhancements:**

- streamline sequence block to session association [\#1541](https://github.com/ilios/ilios/issues/1541)

**Closed issues:**

- Unable to add user to my non-primary school [\#1546](https://github.com/ilios/ilios/issues/1546)
- updating sequence block after un-selecting course throws error. [\#1543](https://github.com/ilios/ilios/issues/1543)

**Merged pull requests:**

- Bump the API version for new CI stuff [\#1548](https://github.com/ilios/ilios/pull/1548) ([jrjohnson](https://github.com/jrjohnson))
- Add authentication for users with different schools [\#1547](https://github.com/ilios/ilios/pull/1547) ([jrjohnson](https://github.com/jrjohnson))
- Update dependencies to latest versions [\#1545](https://github.com/ilios/ilios/pull/1545) ([jrjohnson](https://github.com/jrjohnson))
- allow course prop to be set to null. [\#1544](https://github.com/ilios/ilios/pull/1544) ([stopfstedt](https://github.com/stopfstedt))
- re-defined sequence-block/session relationship. [\#1542](https://github.com/ilios/ilios/pull/1542) ([stopfstedt](https://github.com/stopfstedt))
- apply correct strategy when changing sequence sort order [\#1537](https://github.com/ilios/ilios/pull/1537) ([stopfstedt](https://github.com/stopfstedt))
- annotate the track property as exposed. [\#1536](https://github.com/ilios/ilios/pull/1536) ([stopfstedt](https://github.com/stopfstedt))

## [v3.16.0](https://github.com/ilios/ilios/tree/v3.16.0) (2016-07-29)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.15.0...v3.16.0)

**Implemented enhancements:**

- Add CAS as an Authentication service [\#1522](https://github.com/ilios/ilios/issues/1522)
- Provide "active" status to terms, competencies and vocabularies [\#1519](https://github.com/ilios/ilios/issues/1519)

**Closed issues:**

- Remove deprecated SecureRandom calls [\#1530](https://github.com/ilios/ilios/issues/1530)
- grant course director role to "user zero" in addition to dev role [\#1524](https://github.com/ilios/ilios/issues/1524)
- Session "supplemental" flag always set to "true" [\#1523](https://github.com/ilios/ilios/issues/1523)
- provide test coverage for first user command [\#1516](https://github.com/ilios/ilios/issues/1516)
- First user does not have access to the Admin menu [\#1514](https://github.com/ilios/ilios/issues/1514)
- update info on offering::room in data dictionary [\#1395](https://github.com/ilios/ilios/issues/1395)
- document offering::site in data dictionary [\#1394](https://github.com/ilios/ilios/issues/1394)

**Merged pull requests:**

- updating changelog for v3.16.0 release [\#1535](https://github.com/ilios/ilios/pull/1535) ([thecoolestguy](https://github.com/thecoolestguy))
- fixed coding standards violation. [\#1534](https://github.com/ilios/ilios/pull/1534) ([stopfstedt](https://github.com/stopfstedt))
- make sequence block start/end date optional [\#1533](https://github.com/ilios/ilios/pull/1533) ([stopfstedt](https://github.com/stopfstedt))
- Replace SecureRandom with random\_bytes [\#1532](https://github.com/ilios/ilios/pull/1532) ([jrjohnson](https://github.com/jrjohnson))
- replaced call to non-existent method with one of the same intent that works. [\#1529](https://github.com/ilios/ilios/pull/1529) ([stopfstedt](https://github.com/stopfstedt))
- Add CAS as an auth provider [\#1528](https://github.com/ilios/ilios/pull/1528) ([jrjohnson](https://github.com/jrjohnson))
- Doctrine event listener cleanup [\#1527](https://github.com/ilios/ilios/pull/1527) ([stopfstedt](https://github.com/stopfstedt))
- Resorting sequence blocks [\#1526](https://github.com/ilios/ilios/pull/1526) ([stopfstedt](https://github.com/stopfstedt))
- make "user zero" a course director again. [\#1525](https://github.com/ilios/ilios/pull/1525) ([stopfstedt](https://github.com/stopfstedt))
- added active flag to competency/vocab/term initial data set. [\#1521](https://github.com/ilios/ilios/pull/1521) ([stopfstedt](https://github.com/stopfstedt))
- Add active to competency, term, and vocabulary [\#1520](https://github.com/ilios/ilios/pull/1520) ([jrjohnson](https://github.com/jrjohnson))
- user's, not users. [\#1518](https://github.com/ilios/ilios/pull/1518) ([stopfstedt](https://github.com/stopfstedt))
- Test coverage install first user command [\#1517](https://github.com/ilios/ilios/pull/1517) ([stopfstedt](https://github.com/stopfstedt))
- grant dev role to first user instead of course director. [\#1515](https://github.com/ilios/ilios/pull/1515) ([stopfstedt](https://github.com/stopfstedt))
- Disabled/re-enabled foreign key checks on several migrations to get them to work with MySQL 5.6+ [\#1451](https://github.com/ilios/ilios/pull/1451) ([thecoolestguy](https://github.com/thecoolestguy))

## [v3.15.0](https://github.com/ilios/ilios/tree/v3.15.0) (2016-07-08)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.14.0...v3.15.0)

**Implemented enhancements:**

- Remove colon in token output [\#1506](https://github.com/ilios/ilios/issues/1506)

**Closed issues:**

- Drop support for PHP 5.5 [\#1480](https://github.com/ilios/ilios/issues/1480)

**Merged pull requests:**

- updating changelog for the v3.15.0 release [\#1510](https://github.com/ilios/ilios/pull/1510) ([thecoolestguy](https://github.com/thecoolestguy))
- updating changelog for the v3.14.1 release [\#1509](https://github.com/ilios/ilios/pull/1509) ([thecoolestguy](https://github.com/thecoolestguy))
- Simplify rollover by counting days [\#1508](https://github.com/ilios/ilios/pull/1508) ([jrjohnson](https://github.com/jrjohnson))
- Remove superfluous colon from token output [\#1507](https://github.com/ilios/ilios/pull/1507) ([jrjohnson](https://github.com/jrjohnson))
- Update dependencies [\#1504](https://github.com/ilios/ilios/pull/1504) ([jrjohnson](https://github.com/jrjohnson))
- corrected table joins in query. [\#1502](https://github.com/ilios/ilios/pull/1502) ([stopfstedt](https://github.com/stopfstedt))
- Add PHP7 support, remove 5.5 [\#1481](https://github.com/ilios/ilios/pull/1481) ([jrjohnson](https://github.com/jrjohnson))

## [v3.14.0](https://github.com/ilios/ilios/tree/v3.14.0) (2016-06-30)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.13.0...v3.14.0)

**Implemented enhancements:**

- Take newCourseTitle for rollovers [\#1487](https://github.com/ilios/ilios/issues/1487)
- update identifiers for AAMC Resource Types [\#1484](https://github.com/ilios/ilios/issues/1484)
- Allow sessions to be filtered by multiple courses [\#1466](https://github.com/ilios/ilios/issues/1466)
- Add bulk creation to authentication endpoint [\#1461](https://github.com/ilios/ilios/issues/1461)
- apply additional padding on numeric part of AAMC resource type ids. [\#1495](https://github.com/ilios/ilios/pull/1495) ([stopfstedt](https://github.com/stopfstedt))

**Closed issues:**

- ILMs not rolling over correctly [\#1497](https://github.com/ilios/ilios/issues/1497)
- Session Description not rolling over [\#1496](https://github.com/ilios/ilios/issues/1496)
- Course rollover not accounting for string 'false' [\#1485](https://github.com/ilios/ilios/issues/1485)
- Add tests for course rollover [\#1483](https://github.com/ilios/ilios/issues/1483)
- Configuration settings for change alerts and teaching notifications [\#1482](https://github.com/ilios/ilios/issues/1482)
- Rollover startDate creeps backward [\#1478](https://github.com/ilios/ilios/issues/1478)
- Unable to rollover session linked to orphan course objectives [\#1476](https://github.com/ilios/ilios/issues/1476)
- Add course rollover controller [\#1473](https://github.com/ilios/ilios/issues/1473)
- add d/l token to ci reports [\#1463](https://github.com/ilios/ilios/issues/1463)
- code consolidation [\#1459](https://github.com/ilios/ilios/issues/1459)
- Audit Log Table Not Capturing Create ID's  [\#1445](https://github.com/ilios/ilios/issues/1445)
- getId fails on competency voter [\#1428](https://github.com/ilios/ilios/issues/1428)
- Create course rollover console command [\#1286](https://github.com/ilios/ilios/issues/1286)

**Merged pull requests:**

- updating changelog for the v3.14.0 release [\#1500](https://github.com/ilios/ilios/pull/1500) ([thecoolestguy](https://github.com/thecoolestguy))
- Course rollover sessions and ilm bugfix [\#1499](https://github.com/ilios/ilios/pull/1499) ([jrjohnson](https://github.com/jrjohnson))
- Functional changes to original course rollover to account for date drifting over years [\#1498](https://github.com/ilios/ilios/pull/1498) ([thecoolestguy](https://github.com/thecoolestguy))
- moved the --append flag to the right place. [\#1494](https://github.com/ilios/ilios/pull/1494) ([stopfstedt](https://github.com/stopfstedt))
- replaced hokey dash with default dash. [\#1493](https://github.com/ilios/ilios/pull/1493) ([stopfstedt](https://github.com/stopfstedt))
- Add newCourseTitle parameter to rollover [\#1492](https://github.com/ilios/ilios/pull/1492) ([jrjohnson](https://github.com/jrjohnson))
- converted AAMC resource type identifiers to text [\#1491](https://github.com/ilios/ilios/pull/1491) ([stopfstedt](https://github.com/stopfstedt))
- fixed update instructions. [\#1490](https://github.com/ilios/ilios/pull/1490) ([stopfstedt](https://github.com/stopfstedt))
- More rollover tests [\#1486](https://github.com/ilios/ilios/pull/1486) ([jrjohnson](https://github.com/jrjohnson))
- fixed rollover offset bug. [\#1479](https://github.com/ilios/ilios/pull/1479) ([stopfstedt](https://github.com/stopfstedt))
- Allow rollover for sessions with orphaned parent objectives [\#1477](https://github.com/ilios/ilios/pull/1477) ([jrjohnson](https://github.com/jrjohnson))
- Rollover a course [\#1475](https://github.com/ilios/ilios/pull/1475) ([jrjohnson](https://github.com/jrjohnson))
- course rollover command [\#1469](https://github.com/ilios/ilios/pull/1469) ([stopfstedt](https://github.com/stopfstedt))
- Allow sessions to be filtered by multiple courses [\#1467](https://github.com/ilios/ilios/pull/1467) ([jrjohnson](https://github.com/jrjohnson))
- generate LM d/l token explicitly. [\#1465](https://github.com/ilios/ilios/pull/1465) ([stopfstedt](https://github.com/stopfstedt))
- Ci report dl token [\#1464](https://github.com/ilios/ilios/pull/1464) ([stopfstedt](https://github.com/stopfstedt))
- Bulk add authentication [\#1462](https://github.com/ilios/ilios/pull/1462) ([jrjohnson](https://github.com/jrjohnson))
- replaced \_\_toString method with trait. [\#1460](https://github.com/ilios/ilios/pull/1460) ([stopfstedt](https://github.com/stopfstedt))
- exclude document in payload of curriculum inventory export [\#1458](https://github.com/ilios/ilios/pull/1458) ([stopfstedt](https://github.com/stopfstedt))
- fixed toString magic method. [\#1457](https://github.com/ilios/ilios/pull/1457) ([stopfstedt](https://github.com/stopfstedt))
- log auditable entity creation with record ids. [\#1454](https://github.com/ilios/ilios/pull/1454) ([stopfstedt](https://github.com/stopfstedt))

## [v3.13.0](https://github.com/ilios/ilios/tree/v3.13.0) (2016-06-10)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.12.0...v3.13.0)

**Implemented enhancements:**

- Add configuration options for Shibboleth auth [\#1447](https://github.com/ilios/ilios/issues/1447)
- Allow token to be invalidated [\#1439](https://github.com/ilios/ilios/issues/1439)

**Closed issues:**

- Foreign key error when running DB schema update script [\#1452](https://github.com/ilios/ilios/issues/1452)
- Update Wiki page on Shibboleth authn [\#1450](https://github.com/ilios/ilios/issues/1450)
- allow for cascading deletes from curriculum inventory [\#1441](https://github.com/ilios/ilios/issues/1441)

**Merged pull requests:**

- updating changelog for the v3.13.0 release [\#1455](https://github.com/ilios/ilios/pull/1455) ([thecoolestguy](https://github.com/thecoolestguy))
- Move drop tables further up in update script [\#1453](https://github.com/ilios/ilios/pull/1453) ([jrjohnson](https://github.com/jrjohnson))
- made shibb login/logout path and asserted user id attr configurable. [\#1448](https://github.com/ilios/ilios/pull/1448) ([stopfstedt](https://github.com/stopfstedt))
- rm unique constraint from ci reports. [\#1444](https://github.com/ilios/ilios/pull/1444) ([stopfstedt](https://github.com/stopfstedt))
- enable cascading deletes from curriculum inventory reports. [\#1443](https://github.com/ilios/ilios/pull/1443) ([stopfstedt](https://github.com/stopfstedt))
- Update dependencies and remove dead packages [\#1442](https://github.com/ilios/ilios/pull/1442) ([jrjohnson](https://github.com/jrjohnson))
- Allow tokens to be invalidated at /auth/invalidatetokens [\#1440](https://github.com/ilios/ilios/pull/1440) ([jrjohnson](https://github.com/jrjohnson))
- "atomic" ci report creation [\#1438](https://github.com/ilios/ilios/pull/1438) ([stopfstedt](https://github.com/stopfstedt))
- Update API to v1.5 [\#1434](https://github.com/ilios/ilios/pull/1434) ([jrjohnson](https://github.com/jrjohnson))
- refactor managers and handlers. [\#1426](https://github.com/ilios/ilios/pull/1426) ([stopfstedt](https://github.com/stopfstedt))

## [v3.12.0](https://github.com/ilios/ilios/tree/v3.12.0) (2016-05-27)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.11.0...v3.12.0)

**Implemented enhancements:**

- Add the ability to track learning resources, per MedBiquitous spec/standards [\#566](https://github.com/ilios/ilios/issues/566)

**Closed issues:**

- Audit required for API docs [\#1433](https://github.com/ilios/ilios/issues/1433)
- Error when creating offerings [\#1431](https://github.com/ilios/ilios/issues/1431)
- School not required in course API [\#1424](https://github.com/ilios/ilios/issues/1424)

**Merged pull requests:**

- updating changelog for v3.12.0 release [\#1437](https://github.com/ilios/ilios/pull/1437) ([thecoolestguy](https://github.com/thecoolestguy))
- Align API docs with expectations [\#1436](https://github.com/ilios/ilios/pull/1436) ([jrjohnson](https://github.com/jrjohnson))
- Fix problem with AuditLog [\#1432](https://github.com/ilios/ilios/pull/1432) ([jrjohnson](https://github.com/jrjohnson))
- added call to remove 'fkey\_user\_primary\_school' index [\#1429](https://github.com/ilios/ilios/pull/1429) ([thecoolestguy](https://github.com/thecoolestguy))
- Data correction for v2 to v3 update [\#1427](https://github.com/ilios/ilios/pull/1427) ([jrjohnson](https://github.com/jrjohnson))
- reject course input without school. [\#1425](https://github.com/ilios/ilios/pull/1425) ([stopfstedt](https://github.com/stopfstedt))
- loosen up restrictions on READ access to schools. [\#1423](https://github.com/ilios/ilios/pull/1423) ([stopfstedt](https://github.com/stopfstedt))
- provided general upgrade notes for ilios3. [\#1421](https://github.com/ilios/ilios/pull/1421) ([stopfstedt](https://github.com/stopfstedt))
- added min and max attributes on sequence blocks to ci export. [\#1418](https://github.com/ilios/ilios/pull/1418) ([stopfstedt](https://github.com/stopfstedt))
- added aamc resource type and rigged it up to terms. [\#1399](https://github.com/ilios/ilios/pull/1399) ([stopfstedt](https://github.com/stopfstedt))

## [v3.11.0](https://github.com/ilios/ilios/tree/v3.11.0) (2016-05-13)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.10.0...v3.11.0)

**Implemented enhancements:**

- Provide UI method for managing PCRS/Competency/Objective Mappings [\#486](https://github.com/ilios/ilios/issues/486)

**Closed issues:**

- Program DTO [\#1415](https://github.com/ilios/ilios/issues/1415)
- Upgrade to Symfony 2.8.6 [\#1413](https://github.com/ilios/ilios/issues/1413)
- Filtering parent=null does not work on learnergroups API anymore. [\#1409](https://github.com/ilios/ilios/issues/1409)
- UrlGenerator reference type deprecation [\#1406](https://github.com/ilios/ilios/issues/1406)
- Fix form type deprecation [\#1403](https://github.com/ilios/ilios/issues/1403)
- Quotes in yaml config [\#1401](https://github.com/ilios/ilios/issues/1401)
- fix deprecation notices [\#1400](https://github.com/ilios/ilios/issues/1400)
- Developers should be able to retrieve another users events [\#1391](https://github.com/ilios/ilios/issues/1391)

**Merged pull requests:**

- updating changelog for the v3.11.0 release [\#1419](https://github.com/ilios/ilios/pull/1419) ([thecoolestguy](https://github.com/thecoolestguy))
- Use DTO pattern for Programs API [\#1416](https://github.com/ilios/ilios/pull/1416) ([jrjohnson](https://github.com/jrjohnson))
- updated to symfony 2.8.6 [\#1414](https://github.com/ilios/ilios/pull/1414) ([stopfstedt](https://github.com/stopfstedt))
- fixed path to cached bootstrap file. [\#1412](https://github.com/ilios/ilios/pull/1412) ([stopfstedt](https://github.com/stopfstedt))
- Filter learner groups by parent and cohort [\#1410](https://github.com/ilios/ilios/pull/1410) ([jrjohnson](https://github.com/jrjohnson))
- Stop allowing deprecations in travis builds [\#1408](https://github.com/ilios/ilios/pull/1408) ([jrjohnson](https://github.com/jrjohnson))
- fixed deprecation. [\#1407](https://github.com/ilios/ilios/pull/1407) ([stopfstedt](https://github.com/stopfstedt))
- Clear form deprecations [\#1405](https://github.com/ilios/ilios/pull/1405) ([jrjohnson](https://github.com/jrjohnson))
- quoted scalar values to get rid of deprecation warnings. [\#1404](https://github.com/ilios/ilios/pull/1404) ([stopfstedt](https://github.com/stopfstedt))
- Clear YAML deprecations [\#1402](https://github.com/ilios/ilios/pull/1402) ([jrjohnson](https://github.com/jrjohnson))
- grant view privileges to developers on all published user events [\#1398](https://github.com/ilios/ilios/pull/1398) ([stopfstedt](https://github.com/stopfstedt))
- Latest updates to dependencies [\#1397](https://github.com/ilios/ilios/pull/1397) ([jrjohnson](https://github.com/jrjohnson))

## [v3.10.0](https://github.com/ilios/ilios/tree/v3.10.0) (2016-04-30)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.9.0...v3.10.0)

**Implemented enhancements:**

- Learner Groups DTO [\#1392](https://github.com/ilios/ilios/issues/1392)
- Increase `offering.room` from VARCHAR\(60\) to VARCHAR\(255\) [\#1383](https://github.com/ilios/ilios/issues/1383)
- Add bulk user creation to /users endpoint [\#1375](https://github.com/ilios/ilios/issues/1375)

**Closed issues:**

- args list has redundant commas [\#1388](https://github.com/ilios/ilios/issues/1388)
- ensure that course deletion cascades [\#1386](https://github.com/ilios/ilios/issues/1386)
- Add value of `site` to offering table [\#1384](https://github.com/ilios/ilios/issues/1384)
- What PHP Versions should we support and how often should we change it? [\#1207](https://github.com/ilios/ilios/issues/1207)

**Merged pull requests:**

- Allow multiple users to be added simultaneously [\#1396](https://github.com/ilios/ilios/pull/1396) ([jrjohnson](https://github.com/jrjohnson))
- Convert LearnerGroups endpoint to use DTOs [\#1393](https://github.com/ilios/ilios/pull/1393) ([jrjohnson](https://github.com/jrjohnson))
- add 'site' attribute to 'offering' entity. [\#1390](https://github.com/ilios/ilios/pull/1390) ([stopfstedt](https://github.com/stopfstedt))
- rm redundant commas in args list. [\#1389](https://github.com/ilios/ilios/pull/1389) ([stopfstedt](https://github.com/stopfstedt))
- updated foreign key constraints to allow for cascading deletes. [\#1387](https://github.com/ilios/ilios/pull/1387) ([stopfstedt](https://github.com/stopfstedt))
- bumped up size of offering::room column. [\#1385](https://github.com/ilios/ilios/pull/1385) ([stopfstedt](https://github.com/stopfstedt))
- Fix annotation for SessionDescription [\#1381](https://github.com/ilios/ilios/pull/1381) ([jrjohnson](https://github.com/jrjohnson))
- Require a frontend when starting up [\#1379](https://github.com/ilios/ilios/pull/1379) ([jrjohnson](https://github.com/jrjohnson))

## [v3.9.0](https://github.com/ilios/ilios/tree/v3.9.0) (2016-04-08)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.8.0...v3.9.0)

**Implemented enhancements:**

- MeSH Descriptor DTOs [\#1362](https://github.com/ilios/ilios/issues/1362)

**Closed issues:**

- Error when adding new students without a --schoolId [\#1373](https://github.com/ilios/ilios/issues/1373)
- add DISTINCT clause in DTO queries? [\#1363](https://github.com/ilios/ilios/issues/1363)

**Merged pull requests:**

- updated CHANGELOG.md for v3.9.0 release \[skip-ci\] [\#1377](https://github.com/ilios/ilios/pull/1377) ([thecoolestguy](https://github.com/thecoolestguy))
- Fix typo in select query from SchoolRepository:findBy [\#1374](https://github.com/ilios/ilios/pull/1374) ([jrjohnson](https://github.com/jrjohnson))
- Filter users by role and cohort [\#1371](https://github.com/ilios/ilios/pull/1371) ([jrjohnson](https://github.com/jrjohnson))
- Bump the API version to v1.3 [\#1370](https://github.com/ilios/ilios/pull/1370) ([jrjohnson](https://github.com/jrjohnson))
- Use DTO pattern for Cohorts API [\#1369](https://github.com/ilios/ilios/pull/1369) ([jrjohnson](https://github.com/jrjohnson))
- Convert School to DTO for GET requests [\#1368](https://github.com/ilios/ilios/pull/1368) ([jrjohnson](https://github.com/jrjohnson))
- Added filtering by relationships for cohorts API [\#1367](https://github.com/ilios/ilios/pull/1367) ([jrjohnson](https://github.com/jrjohnson))
- Program Years as DTOs [\#1366](https://github.com/ilios/ilios/pull/1366) ([jrjohnson](https://github.com/jrjohnson))
- use DTOs for MeSH descriptor queries [\#1365](https://github.com/ilios/ilios/pull/1365) ([stopfstedt](https://github.com/stopfstedt))
- added distinct clause to queries. [\#1364](https://github.com/ilios/ilios/pull/1364) ([stopfstedt](https://github.com/stopfstedt))
- Fix error handling [\#1360](https://github.com/ilios/ilios/pull/1360) ([jrjohnson](https://github.com/jrjohnson))

## [v3.8.0](https://github.com/ilios/ilios/tree/v3.8.0) (2016-03-22)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.7.0...v3.8.0)

**Implemented enhancements:**

- migrate topics to terms/vocab and purge topics from code and schema [\#1280](https://github.com/ilios/ilios/issues/1280)
- change ICS feed size window [\#1230](https://github.com/ilios/ilios/issues/1230)

**Closed issues:**

- When filtering courses by instructors include directors [\#1356](https://github.com/ilios/ilios/issues/1356)
- deprecate MeSH Semantic Type components [\#1350](https://github.com/ilios/ilios/issues/1350)
- Enable APC loader in app.php [\#1347](https://github.com/ilios/ilios/issues/1347)

**Merged pull requests:**

- updating changelog for the v3.8.0 release [\#1359](https://github.com/ilios/ilios/pull/1359) ([thecoolestguy](https://github.com/thecoolestguy))
- include directors when filtering courses by instructors. [\#1358](https://github.com/ilios/ilios/pull/1358) ([stopfstedt](https://github.com/stopfstedt))
- Term children should not be saved with the term [\#1357](https://github.com/ilios/ilios/pull/1357) ([jrjohnson](https://github.com/jrjohnson))
- Save and Load terms completly [\#1355](https://github.com/ilios/ilios/pull/1355) ([jrjohnson](https://github.com/jrjohnson))
- replaced IRC with Slack token. [\#1353](https://github.com/ilios/ilios/pull/1353) ([stopfstedt](https://github.com/stopfstedt))
- flagged semantic type components as deprecated. [\#1351](https://github.com/ilios/ilios/pull/1351) ([stopfstedt](https://github.com/stopfstedt))
- enabled APC loader. [\#1349](https://github.com/ilios/ilios/pull/1349) ([stopfstedt](https://github.com/stopfstedt))
- Allow access to app\_dev.php by proxies [\#1348](https://github.com/ilios/ilios/pull/1348) ([jrjohnson](https://github.com/jrjohnson))
- Remove topic reports which are no longer valid [\#1346](https://github.com/ilios/ilios/pull/1346) ([jrjohnson](https://github.com/jrjohnson))
- Increase the ICS feed window by removing course objectives [\#1345](https://github.com/ilios/ilios/pull/1345) ([jrjohnson](https://github.com/jrjohnson))
- Bump the api version to 1.2 [\#1344](https://github.com/ilios/ilios/pull/1344) ([jrjohnson](https://github.com/jrjohnson))
- migrate topics to terms and vocabs, rm topics [\#1281](https://github.com/ilios/ilios/pull/1281) ([stopfstedt](https://github.com/stopfstedt))

## [v3.7.0](https://github.com/ilios/ilios/tree/v3.7.0) (2016-03-02)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.6.0...v3.7.0)

**Implemented enhancements:**

- Default dashboard to primary school for user [\#1321](https://github.com/ilios/ilios/issues/1321)

**Closed issues:**

- New citation learning materials should get the mime-type citation [\#1339](https://github.com/ilios/ilios/issues/1339)
- Disabled users are allowed to login [\#1332](https://github.com/ilios/ilios/issues/1332)
- How shall we handle frontend updates [\#1062](https://github.com/ilios/ilios/issues/1062)

**Merged pull requests:**

- adding CHANGELOG for v3.7.0 release [\#1343](https://github.com/ilios/ilios/pull/1343) ([thecoolestguy](https://github.com/thecoolestguy))
- Update composer libraries [\#1341](https://github.com/ilios/ilios/pull/1341) ([jrjohnson](https://github.com/jrjohnson))
- Link and Citation learning materials get a corresponding mimetype [\#1340](https://github.com/ilios/ilios/pull/1340) ([jrjohnson](https://github.com/jrjohnson))
- Allow an empty password to be passed to authentication POST [\#1338](https://github.com/ilios/ilios/pull/1338) ([jrjohnson](https://github.com/jrjohnson))
- Improve directory [\#1337](https://github.com/ilios/ilios/pull/1337) ([jrjohnson](https://github.com/jrjohnson))
- Add duplicate protection to entities [\#1336](https://github.com/ilios/ilios/pull/1336) ([jrjohnson](https://github.com/jrjohnson))
- New PendingUsers filters [\#1335](https://github.com/ilios/ilios/pull/1335) ([jrjohnson](https://github.com/jrjohnson))
- Update the frontend automatically [\#1334](https://github.com/ilios/ilios/pull/1334) ([jrjohnson](https://github.com/jrjohnson))
- Prevent disabled users from logging in [\#1333](https://github.com/ilios/ilios/pull/1333) ([jrjohnson](https://github.com/jrjohnson))
- Add UserDTO [\#1331](https://github.com/ilios/ilios/pull/1331) ([jrjohnson](https://github.com/jrjohnson))

## [v3.6.0](https://github.com/ilios/ilios/tree/v3.6.0) (2016-02-12)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.5.0...v3.6.0)

**Implemented enhancements:**

- Extend/exchange topics tagging model with robust taxonomies [\#1257](https://github.com/ilios/ilios/issues/1257)

**Closed issues:**

- 403/access denied error when requesting instructor groups from the dashboard calendar. [\#1322](https://github.com/ilios/ilios/issues/1322)
- Loading all sessions is too memory intensive [\#1313](https://github.com/ilios/ilios/issues/1313)
- Learner Groups with Learners From Multiple Schools Not Displaying [\#1307](https://github.com/ilios/ilios/issues/1307)
- When saving terms allow realtionships to be saved as well [\#1354](https://github.com/ilios/ilios/issues/1354)
- Loading all courses is too memory intensive [\#1312](https://github.com/ilios/ilios/issues/1312)

**Merged pull requests:**

- disable profiler in test env. [\#1330](https://github.com/ilios/ilios/pull/1330) ([stopfstedt](https://github.com/stopfstedt))
- Make it easier to add a new user [\#1329](https://github.com/ilios/ilios/pull/1329) ([jrjohnson](https://github.com/jrjohnson))
- session DTO [\#1328](https://github.com/ilios/ilios/pull/1328) ([stopfstedt](https://github.com/stopfstedt))
- Voters cleanup [\#1327](https://github.com/ilios/ilios/pull/1327) ([stopfstedt](https://github.com/stopfstedt))
- Minor version bumps for third party libs [\#1326](https://github.com/ilios/ilios/pull/1326) ([jrjohnson](https://github.com/jrjohnson))
- Course as a Data Transfer Object [\#1325](https://github.com/ilios/ilios/pull/1325) ([jrjohnson](https://github.com/jrjohnson))
- losened up views perms. [\#1324](https://github.com/ilios/ilios/pull/1324) ([stopfstedt](https://github.com/stopfstedt))
- losened up view perms on instructor groups. [\#1323](https://github.com/ilios/ilios/pull/1323) ([stopfstedt](https://github.com/stopfstedt))

## [v3.5.0](https://github.com/ilios/ilios/tree/v3.5.0) (2016-02-05)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.4.1...v3.5.0)

**Implemented enhancements:**

- Filter session type by course [\#1316](https://github.com/ilios/ilios/issues/1316)
- Filter session type by learning material [\#1315](https://github.com/ilios/ilios/issues/1315)
- Filter session type by program [\#1314](https://github.com/ilios/ilios/issues/1314)

**Closed issues:**

- fix symfony deprecations [\#1300](https://github.com/ilios/ilios/issues/1300)
- View Permissions On Session Offerings Is Too Stringent [\#1251](https://github.com/ilios/ilios/issues/1251)
- raise PHP minimum required version to 5.5 [\#1302](https://github.com/ilios/ilios/issues/1302)

**Merged pull requests:**

- filter session type by program. [\#1319](https://github.com/ilios/ilios/pull/1319) ([stopfstedt](https://github.com/stopfstedt))
- filter session type by learning material. [\#1318](https://github.com/ilios/ilios/pull/1318) ([stopfstedt](https://github.com/stopfstedt))
- filter session type by courses. [\#1317](https://github.com/ilios/ilios/pull/1317) ([stopfstedt](https://github.com/stopfstedt))
- rm obsolete, soft-deletes-related code. [\#1310](https://github.com/ilios/ilios/pull/1310) ([stopfstedt](https://github.com/stopfstedt))
- reworked various read permissions checks [\#1306](https://github.com/ilios/ilios/pull/1306) ([stopfstedt](https://github.com/stopfstedt))
- Remove support for PHP 5.4 [\#1303](https://github.com/ilios/ilios/pull/1303) ([jrjohnson](https://github.com/jrjohnson))
- get rid of deprecation warnings [\#1301](https://github.com/ilios/ilios/pull/1301) ([stopfstedt](https://github.com/stopfstedt))

## [v3.4.1](https://github.com/ilios/ilios/tree/v3.4.1) (2016-02-02)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.4.0...v3.4.1)

**Closed issues:**

- Reminder Emails Should Not Go Out on Not Published Courses and Sessions [\#1308](https://github.com/ilios/ilios/issues/1308)

**Merged pull requests:**

- filter unpublished offerings out of teaching reminders [\#1309](https://github.com/ilios/ilios/pull/1309) ([stopfstedt](https://github.com/stopfstedt))

## [v3.4.0](https://github.com/ilios/ilios/tree/v3.4.0) (2016-02-01)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.3.1...v3.4.0)

**Implemented enhancements:**

- Add user search type to /application/config [\#1291](https://github.com/ilios/ilios/issues/1291)
- Add /application/directory endpoint [\#1290](https://github.com/ilios/ilios/issues/1290)

**Closed issues:**

- consider session modified timestamp in school/user-events [\#1299](https://github.com/ilios/ilios/issues/1299)

**Merged pull requests:**

- Updating a user should not update all of their offerings [\#1305](https://github.com/ilios/ilios/pull/1305) ([jrjohnson](https://github.com/jrjohnson))
- uses newer updated-timestamp from offerings and sessions in calendar events. [\#1304](https://github.com/ilios/ilios/pull/1304) ([stopfstedt](https://github.com/stopfstedt))
- replaced deprecated init\(\) method with constructor. [\#1298](https://github.com/ilios/ilios/pull/1298) ([stopfstedt](https://github.com/stopfstedt))
- defined getFirstAndLastName\(\) in UserInterface. [\#1297](https://github.com/ilios/ilios/pull/1297) ([stopfstedt](https://github.com/stopfstedt))
- Add /application/directory/search endpoint [\#1296](https://github.com/ilios/ilios/pull/1296) ([jrjohnson](https://github.com/jrjohnson))
- corrected typo in variable name. [\#1293](https://github.com/ilios/ilios/pull/1293) ([stopfstedt](https://github.com/stopfstedt))
- Add a userSearchType value to config [\#1292](https://github.com/ilios/ilios/pull/1292) ([jrjohnson](https://github.com/jrjohnson))
- Add PHP 5.6 to the PHP versions we test against [\#1289](https://github.com/ilios/ilios/pull/1289) ([jrjohnson](https://github.com/jrjohnson))
- Created Add User Command [\#1287](https://github.com/ilios/ilios/pull/1287) ([jrjohnson](https://github.com/jrjohnson))
- Rm broken namespace import [\#1285](https://github.com/ilios/ilios/pull/1285) ([stopfstedt](https://github.com/stopfstedt))
- fixed typo in class docblock. [\#1284](https://github.com/ilios/ilios/pull/1284) ([stopfstedt](https://github.com/stopfstedt))
- replaced id-getter/setters with identifable entity trait. [\#1283](https://github.com/ilios/ilios/pull/1283) ([stopfstedt](https://github.com/stopfstedt))
- declared cohort-getter/setters in program-year entity interface. [\#1282](https://github.com/ilios/ilios/pull/1282) ([stopfstedt](https://github.com/stopfstedt))
- Add new taxonomy system [\#1279](https://github.com/ilios/ilios/pull/1279) ([stopfstedt](https://github.com/stopfstedt))

## [v3.3.1](https://github.com/ilios/ilios/tree/v3.3.1) (2016-01-25)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.3.0...v3.3.1)

**Merged pull requests:**

- updated frontend build hash to non-corrupted one [\#1278](https://github.com/ilios/ilios/pull/1278) ([thecoolestguy](https://github.com/thecoolestguy))

## [v3.3.0](https://github.com/ilios/ilios/tree/v3.3.0) (2016-01-23)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.2.0...v3.3.0)

**Implemented enhancements:**

- Allow for email name AND address in messaging console commands [\#1214](https://github.com/ilios/ilios/issues/1214)

**Closed issues:**

- Changing ILM to non-ILM status throws 500 error [\#1267](https://github.com/ilios/ilios/issues/1267)
- Add https redirection by default in .htaccess [\#1261](https://github.com/ilios/ilios/issues/1261)
- Unable to Add Learner To Learner Group [\#1259](https://github.com/ilios/ilios/issues/1259)
- Symfony 2.8: update deprecated Voter implementations [\#1220](https://github.com/ilios/ilios/issues/1220)
- Handle errors fetching the frontend index file [\#1209](https://github.com/ilios/ilios/issues/1209)
- Update Data Dictionary for 3.0 [\#969](https://github.com/ilios/ilios/issues/969)
- Add more detail to delete exceptions [\#908](https://github.com/ilios/ilios/issues/908)

**Merged pull requests:**

- Updated changelog and frontend version for v3.3.0 [\#1276](https://github.com/ilios/ilios/pull/1276) ([thecoolestguy](https://github.com/thecoolestguy))
- Configurable forcing of https [\#1275](https://github.com/ilios/ilios/pull/1275) ([jrjohnson](https://github.com/jrjohnson))
- set join columns to be not null [\#1274](https://github.com/ilios/ilios/pull/1274) ([stopfstedt](https://github.com/stopfstedt))
- Provide more detailed logs when deletion doesn't work [\#1273](https://github.com/ilios/ilios/pull/1273) ([jrjohnson](https://github.com/jrjohnson))
- reject student access to in-draft LMs [\#1272](https://github.com/ilios/ilios/pull/1272) ([stopfstedt](https://github.com/stopfstedt))
- added 'sender\_name' option to "send teaching reminders" command [\#1271](https://github.com/ilios/ilios/pull/1271) ([stopfstedt](https://github.com/stopfstedt))
- allow for un-setting of ilm on session. [\#1269](https://github.com/ilios/ilios/pull/1269) ([stopfstedt](https://github.com/stopfstedt))
- Stop logging events in dev logs [\#1268](https://github.com/ilios/ilios/pull/1268) ([jrjohnson](https://github.com/jrjohnson))
- throw an exception if remote file could not be loaded. [\#1266](https://github.com/ilios/ilios/pull/1266) ([stopfstedt](https://github.com/stopfstedt))
- check for pre-existing relationship before adding learner group to ilm [\#1265](https://github.com/ilios/ilios/pull/1265) ([stopfstedt](https://github.com/stopfstedt))

## [v3.2.0](https://github.com/ilios/ilios/tree/v3.2.0) (2016-01-16)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.1.0...v3.2.0)

**Implemented enhancements:**

- query user events across multiple schools [\#1250](https://github.com/ilios/ilios/issues/1250)
- add course title to events [\#1258](https://github.com/ilios/ilios/issues/1258)
- Update IcsController to use events instructors for Taught By [\#1204](https://github.com/ilios/ilios/issues/1204)
- refactor publish events [\#945](https://github.com/ilios/ilios/issues/945)

**Closed issues:**

- Teaching Reminders have HTML entities in them [\#1228](https://github.com/ilios/ilios/issues/1228)
- course directors missing ILM-based user events in their calendar [\#1260](https://github.com/ilios/ilios/issues/1260)
- Updating a course learning material removes the original file reference [\#1253](https://github.com/ilios/ilios/issues/1253)
- Replace deprecated publish event [\#1041](https://github.com/ilios/ilios/issues/1041)

**Merged pull requests:**

- expose course title on calendar events [\#1264](https://github.com/ilios/ilios/pull/1264) ([stopfstedt](https://github.com/stopfstedt))
- expanded queries to pull ILm-based events for directed courses [\#1263](https://github.com/ilios/ilios/pull/1263) ([stopfstedt](https://github.com/stopfstedt))
- Html entity escaping in notifications [\#1256](https://github.com/ilios/ilios/pull/1256) ([stopfstedt](https://github.com/stopfstedt))
- upgrading voters to extend from new symfony class. [\#1255](https://github.com/ilios/ilios/pull/1255) ([stopfstedt](https://github.com/stopfstedt))
- Don't set relativePath for learning materials [\#1254](https://github.com/ilios/ilios/pull/1254) ([jrjohnson](https://github.com/jrjohnson))
- Move publishing migration to the right place in time [\#1252](https://github.com/ilios/ilios/pull/1252) ([jrjohnson](https://github.com/jrjohnson))
- Publishing Simplified [\#1212](https://github.com/ilios/ilios/pull/1212) ([jrjohnson](https://github.com/jrjohnson))

## [v3.1.0](https://github.com/ilios/ilios/tree/v3.1.0) (2016-01-11)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0...v3.1.0)

**Implemented enhancements:**

- streamline query for school events [\#1242](https://github.com/ilios/ilios/issues/1242)
- Convert ILM due dates from dates to date-times [\#1233](https://github.com/ilios/ilios/issues/1233)

**Closed issues:**

- events should be flagged as TBD if owning session or course is published as TBD [\#1247](https://github.com/ilios/ilios/issues/1247)
- filter out offerings in teaching reminders owned by unpublished courses. [\#1244](https://github.com/ilios/ilios/issues/1244)
- filter out user/school events from published session/unpublished course as applicable [\#1240](https://github.com/ilios/ilios/issues/1240)
- calendar feed is sending unpublished events [\#1235](https://github.com/ilios/ilios/issues/1235)
- deletes not cascading [\#1231](https://github.com/ilios/ilios/issues/1231)
- ics generation is borked [\#1225](https://github.com/ilios/ilios/issues/1225)
- Change alert date time values are in utc [\#1223](https://github.com/ilios/ilios/issues/1223)
- ILM dates are off in ICS feed  [\#1222](https://github.com/ilios/ilios/issues/1222)
- Teaching Reminder Emails Are Off Because of the UTC Time Zone Issue [\#1221](https://github.com/ilios/ilios/issues/1221)
- allow for querying the API for "my courses" [\#1210](https://github.com/ilios/ilios/issues/1210)
- Learning Material migration needs step to skip already-migrated files to save time [\#1168](https://github.com/ilios/ilios/issues/1168)
- Limit scheduled user and school event data for students [\#1237](https://github.com/ilios/ilios/issues/1237)
- Update to Symfony 2.8 [\#1206](https://github.com/ilios/ilios/issues/1206)
- Remove sessions and programYears from topics endpoint [\#1203](https://github.com/ilios/ilios/issues/1203)
- Additional fields for user query [\#1142](https://github.com/ilios/ilios/issues/1142)
- Remove alerts from the School endpoint [\#937](https://github.com/ilios/ilios/issues/937)

**Merged pull requests:**

- various bug fixes in method that adds instructors to events [\#1249](https://github.com/ilios/ilios/pull/1249) ([stopfstedt](https://github.com/stopfstedt))
- checks courses for published as tbd status when flagging events as scheduled [\#1248](https://github.com/ilios/ilios/pull/1248) ([stopfstedt](https://github.com/stopfstedt))
- streamlines school events queries [\#1246](https://github.com/ilios/ilios/pull/1246) ([stopfstedt](https://github.com/stopfstedt))
- filter out offerings belonging to unpublished courses. [\#1245](https://github.com/ilios/ilios/pull/1245) ([stopfstedt](https://github.com/stopfstedt))
- Limit Data in Scheduled Events [\#1243](https://github.com/ilios/ilios/pull/1243) ([jrjohnson](https://github.com/jrjohnson))
- Cascading session deletes [\#1239](https://github.com/ilios/ilios/pull/1239) ([stopfstedt](https://github.com/stopfstedt))
- Hide unpublished info from the ICS feed [\#1236](https://github.com/ilios/ilios/pull/1236) ([jrjohnson](https://github.com/jrjohnson))
- Ilm due date datetime conversion [\#1234](https://github.com/ilios/ilios/pull/1234) ([stopfstedt](https://github.com/stopfstedt))
- Update shibboleth config to not require https [\#1232](https://github.com/ilios/ilios/pull/1232) ([jrjohnson](https://github.com/jrjohnson))
- Limit ICS feed events to 1 month back and 2 months forward [\#1229](https://github.com/ilios/ilios/pull/1229) ([jrjohnson](https://github.com/jrjohnson))
- Backup to a working version of our ical library [\#1227](https://github.com/ilios/ilios/pull/1227) ([jrjohnson](https://github.com/jrjohnson))
- date time adjustment hack for ILMs in ICS calendar feed. [\#1226](https://github.com/ilios/ilios/pull/1226) ([stopfstedt](https://github.com/stopfstedt))
- Fix timezone offset in notifications [\#1224](https://github.com/ilios/ilios/pull/1224) ([stopfstedt](https://github.com/stopfstedt))
- return response from controller, don't send it. [\#1219](https://github.com/ilios/ilios/pull/1219) ([stopfstedt](https://github.com/stopfstedt))
- adjust user/school event query ranges for ILMs [\#1218](https://github.com/ilios/ilios/pull/1218) ([stopfstedt](https://github.com/stopfstedt))
- loosen up restrictions for viewing users. [\#1217](https://github.com/ilios/ilios/pull/1217) ([stopfstedt](https://github.com/stopfstedt))
- restrict access to view un-published events for students. [\#1215](https://github.com/ilios/ilios/pull/1215) ([stopfstedt](https://github.com/stopfstedt))
- get current user from token storage instead of from security context service [\#1213](https://github.com/ilios/ilios/pull/1213) ([stopfstedt](https://github.com/stopfstedt))
- Gimme my courses [\#1211](https://github.com/ilios/ilios/pull/1211) ([stopfstedt](https://github.com/stopfstedt))
- Update symphony to 2.8 [\#1208](https://github.com/ilios/ilios/pull/1208) ([jrjohnson](https://github.com/jrjohnson))
- Remove courses, sessions, and programYears from topic endpoint [\#1205](https://github.com/ilios/ilios/pull/1205) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0](https://github.com/ilios/ilios/tree/v3.0.0) (2015-12-15)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-rc1...v3.0.0)

**Implemented enhancements:**

- expose default locale in config object [\#1176](https://github.com/ilios/ilios/issues/1176)
- move auth/config to a new route [\#1175](https://github.com/ilios/ilios/issues/1175)

**Closed issues:**

- Courses filtered by competencies not complete [\#1191](https://github.com/ilios/ilios/issues/1191)
- LM download responds with 500 error. [\#1190](https://github.com/ilios/ilios/issues/1190)
- Developers don't have access to all schools [\#1188](https://github.com/ilios/ilios/issues/1188)
- Update README for release [\#1185](https://github.com/ilios/ilios/issues/1185)
- Add filter by school to report subject endpoints [\#1183](https://github.com/ilios/ilios/issues/1183)
- Update our Travis build/test process to run on container based infrastructure [\#1182](https://github.com/ilios/ilios/issues/1182)
- Allow reports to be scoped by school [\#1180](https://github.com/ilios/ilios/issues/1180)
- perms issue: students cannot see instructors in calendar event details [\#1169](https://github.com/ilios/ilios/issues/1169)
- 500 server error during user model update [\#1167](https://github.com/ilios/ilios/issues/1167)
- userSyncIgnore property in the user model does not keep state in the server [\#1166](https://github.com/ilios/ilios/issues/1166)
- Unable to logout of shibboleth [\#1164](https://github.com/ilios/ilios/issues/1164)
- Cache busting for index.html asset [\#1156](https://github.com/ilios/ilios/issues/1156)
- add default configuration file. [\#1149](https://github.com/ilios/ilios/issues/1149)
- How should we package Ilios for deployment to other campuses [\#1147](https://github.com/ilios/ilios/issues/1147)
- Change our License to MIT [\#1146](https://github.com/ilios/ilios/issues/1146)
- Install guide should cover string cleanup [\#1145](https://github.com/ilios/ilios/issues/1145)
- Install guide should cover learning materials [\#1144](https://github.com/ilios/ilios/issues/1144)
- Update Hardware requirements in documentation [\#1143](https://github.com/ilios/ilios/issues/1143)
- Allow Querying for session type by relationship [\#1140](https://github.com/ilios/ilios/issues/1140)
- Allow querying for mesh descriptor by relationship [\#1139](https://github.com/ilios/ilios/issues/1139)
- Allow querying for topic by relationshp [\#1138](https://github.com/ilios/ilios/issues/1138)
- Allow querying for competency be relationship [\#1137](https://github.com/ilios/ilios/issues/1137)
- Allow querying for learning material by relationship [\#1136](https://github.com/ilios/ilios/issues/1136)
- Allow querying for instructor group by relationship [\#1135](https://github.com/ilios/ilios/issues/1135)
- Allow querying for users as instructors by relationship [\#1134](https://github.com/ilios/ilios/issues/1134)
- Allow querying by program years relationship [\#1133](https://github.com/ilios/ilios/issues/1133)
- Allow querying by relationship for programs endpoint [\#1132](https://github.com/ilios/ilios/issues/1132)
- Multiday events not being queried correctly [\#1131](https://github.com/ilios/ilios/issues/1131)
- Clean up strings in database [\#1128](https://github.com/ilios/ilios/issues/1128)
- Filter courses by user [\#1125](https://github.com/ilios/ilios/issues/1125)
- Update firebase JWT library [\#1120](https://github.com/ilios/ilios/issues/1120)
- Problems editing program year [\#1119](https://github.com/ilios/ilios/issues/1119)
- Permissions Problems editing a course [\#1118](https://github.com/ilios/ilios/issues/1118)
- Unable to delete program year [\#1117](https://github.com/ilios/ilios/issues/1117)
- Learning material description should not be a required field [\#1116](https://github.com/ilios/ilios/issues/1116)
- separate auth token implementation from jwt handling [\#953](https://github.com/ilios/ilios/issues/953)
- Offering filtered by learnerGroup results in code 500 [\#805](https://github.com/ilios/ilios/issues/805)

**Merged pull requests:**

- CHANGELOG updated for v3.0.0, config.yml updated to reflect Ilios Frontend 1.0.0 [\#1202](https://github.com/ilios/ilios/pull/1202) ([thecoolestguy](https://github.com/thecoolestguy))
- Update project README [\#1201](https://github.com/ilios/ilios/pull/1201) ([jrjohnson](https://github.com/jrjohnson))
- Updates to installation and upgrade documentation [\#1199](https://github.com/ilios/ilios/pull/1199) ([thecoolestguy](https://github.com/thecoolestguy))
- Move some setup commands to maintenance [\#1198](https://github.com/ilios/ilios/pull/1198) ([jrjohnson](https://github.com/jrjohnson))
- Update README [\#1197](https://github.com/ilios/ilios/pull/1197) ([saschaben](https://github.com/saschaben))
- Improved Learning Material Migration [\#1195](https://github.com/ilios/ilios/pull/1195) ([jrjohnson](https://github.com/jrjohnson))
- corrects how courses are filtered by competencies [\#1194](https://github.com/ilios/ilios/pull/1194) ([stopfstedt](https://github.com/stopfstedt))
- Give developers read access to schools [\#1189](https://github.com/ilios/ilios/pull/1189) ([jrjohnson](https://github.com/jrjohnson))
- filter entities by schools [\#1187](https://github.com/ilios/ilios/pull/1187) ([stopfstedt](https://github.com/stopfstedt))
- updated ilios frontend release hash. [\#1186](https://github.com/ilios/ilios/pull/1186) ([stopfstedt](https://github.com/stopfstedt))
- More test groups [\#1184](https://github.com/ilios/ilios/pull/1184) ([jrjohnson](https://github.com/jrjohnson))
- Add school relationship to reports [\#1181](https://github.com/ilios/ilios/pull/1181) ([jrjohnson](https://github.com/jrjohnson))
- find users by campus id and username in search [\#1179](https://github.com/ilios/ilios/pull/1179) ([stopfstedt](https://github.com/stopfstedt))
- Update web config with new AWS URLs [\#1178](https://github.com/ilios/ilios/pull/1178) ([jrjohnson](https://github.com/jrjohnson))
- moved config route/expose locale [\#1177](https://github.com/ilios/ilios/pull/1177) ([stopfstedt](https://github.com/stopfstedt))
- updated JWT libs. [\#1174](https://github.com/ilios/ilios/pull/1174) ([stopfstedt](https://github.com/stopfstedt))
- switched out AWS bucket id. [\#1173](https://github.com/ilios/ilios/pull/1173) ([stopfstedt](https://github.com/stopfstedt))
- add instructors to user and school events. [\#1172](https://github.com/ilios/ilios/pull/1172) ([stopfstedt](https://github.com/stopfstedt))
- Protect learner group related entities from double add [\#1171](https://github.com/ilios/ilios/pull/1171) ([jrjohnson](https://github.com/jrjohnson))
- Add userSyncIgnore to User endpoint [\#1170](https://github.com/ilios/ilios/pull/1170) ([jrjohnson](https://github.com/jrjohnson))
- filter instructors by associated entities [\#1165](https://github.com/ilios/ilios/pull/1165) ([stopfstedt](https://github.com/stopfstedt))
- filter instructor groups by associated entities [\#1163](https://github.com/ilios/ilios/pull/1163) ([stopfstedt](https://github.com/stopfstedt))
- Cache index.html for 60 seconds only [\#1162](https://github.com/ilios/ilios/pull/1162) ([jrjohnson](https://github.com/jrjohnson))
- filter topics by associated entities [\#1161](https://github.com/ilios/ilios/pull/1161) ([stopfstedt](https://github.com/stopfstedt))
- implemented custom findBy\(\) in programyear repo. [\#1160](https://github.com/ilios/ilios/pull/1160) ([stopfstedt](https://github.com/stopfstedt))
- filter competencies by associated entities [\#1159](https://github.com/ilios/ilios/pull/1159) ([stopfstedt](https://github.com/stopfstedt))
- filter mesh descriptors by associated entities [\#1158](https://github.com/ilios/ilios/pull/1158) ([stopfstedt](https://github.com/stopfstedt))
- Filter programs be relationships [\#1157](https://github.com/ilios/ilios/pull/1157) ([jrjohnson](https://github.com/jrjohnson))
- Add symphony security checker to travis build [\#1155](https://github.com/ilios/ilios/pull/1155) ([jrjohnson](https://github.com/jrjohnson))
- replaced GPL with MIT license. [\#1154](https://github.com/ilios/ilios/pull/1154) ([stopfstedt](https://github.com/stopfstedt))
- include ilm sessions when filtering by instructor/instructor-groups. [\#1153](https://github.com/ilios/ilios/pull/1153) ([stopfstedt](https://github.com/stopfstedt))
- Correct handling of milti day events for users and schools [\#1152](https://github.com/ilios/ilios/pull/1152) ([jrjohnson](https://github.com/jrjohnson))
- Add relationship filters to LearningMaterial endpoint [\#1151](https://github.com/ilios/ilios/pull/1151) ([jrjohnson](https://github.com/jrjohnson))
- filter session types by associated entities [\#1150](https://github.com/ilios/ilios/pull/1150) ([stopfstedt](https://github.com/stopfstedt))
- Cleanup HTML in database [\#1141](https://github.com/ilios/ilios/pull/1141) ([jrjohnson](https://github.com/jrjohnson))
- Allow program years to be removed [\#1130](https://github.com/ilios/ilios/pull/1130) ([jrjohnson](https://github.com/jrjohnson))
- Learning material description should not be required [\#1129](https://github.com/ilios/ilios/pull/1129) ([jrjohnson](https://github.com/jrjohnson))
- removed obsolete code. [\#1127](https://github.com/ilios/ilios/pull/1127) ([stopfstedt](https://github.com/stopfstedt))
- Add users as a filter on courses endpoint [\#1126](https://github.com/ilios/ilios/pull/1126) ([jrjohnson](https://github.com/jrjohnson))
- Allow sessions to be filtered by related entities needed for reporting [\#1124](https://github.com/ilios/ilios/pull/1124) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0-rc1](https://github.com/ilios/ilios/tree/v3.0.0-rc1) (2015-11-16)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta9...v3.0.0-rc1)

**Implemented enhancements:**

- whitelist underscore element [\#1089](https://github.com/ilios/ilios/issues/1089)
- Permission API needed [\#1078](https://github.com/ilios/ilios/issues/1078)
- decide which fields on the API to escape in order to allow the display of input HTML [\#1047](https://github.com/ilios/ilios/issues/1047)
- re-implement change alert creation [\#1037](https://github.com/ilios/ilios/issues/1037)
- Add ICS generator and ICS calendar feed [\#1010](https://github.com/ilios/ilios/issues/1010)
- on demand jwt invalidation [\#954](https://github.com/ilios/ilios/issues/954)
- Authorize API Access [\#923](https://github.com/ilios/ilios/issues/923)

**Closed issues:**

- course list no longer filtering properly [\#1114](https://github.com/ilios/ilios/issues/1114)
- Course filter by array of ids is broken [\#1112](https://github.com/ilios/ilios/issues/1112)
- break travis test runs up into two [\#1103](https://github.com/ilios/ilios/issues/1103)
- Remove users from UserRole endpoint [\#1102](https://github.com/ilios/ilios/issues/1102)
- Remove sessions from SessionType endpoint [\#1101](https://github.com/ilios/ilios/issues/1101)
- Remove Alerts from User and School endpoint [\#1100](https://github.com/ilios/ilios/issues/1100)
- Students cannot view the learner groups they are in [\#1098](https://github.com/ilios/ilios/issues/1098)
- Instructors in a course which is not in their primary school should have access to that course [\#1091](https://github.com/ilios/ilios/issues/1091)
- Remove soft delete as a concept [\#1085](https://github.com/ilios/ilios/issues/1085)
- Need to invert the year selection list on Courses and Sessions [\#1084](https://github.com/ilios/ilios/issues/1084)
- Program Short Title Should NOT Be Required [\#1083](https://github.com/ilios/ilios/issues/1083)
- Program Title Should Be Required [\#1082](https://github.com/ilios/ilios/issues/1082)
- simplify rules in learning materials voter [\#1080](https://github.com/ilios/ilios/issues/1080)
- Unable To Add Instructor\(s\) To ILM Session [\#1072](https://github.com/ilios/ilios/issues/1072)
- Manage manyToMany collections on the inverse side [\#1069](https://github.com/ilios/ilios/issues/1069)
- Add /errors endpoint to take frontend logs [\#1063](https://github.com/ilios/ilios/issues/1063)
- Add description to searchable fields for learning materials [\#1057](https://github.com/ilios/ilios/issues/1057)
- small group generator throwing 400 error [\#1051](https://github.com/ilios/ilios/issues/1051)
- adjust getter chaining to soft deletes. [\#958](https://github.com/ilios/ilios/issues/958)
- Bad Request 400 Error on Put - Save Session Offering Date [\#947](https://github.com/ilios/ilios/issues/947)
- unable to assign user to instructor group [\#1070](https://github.com/ilios/ilios/issues/1070)
- Don't allow many to one relationships to be saved [\#1059](https://github.com/ilios/ilios/issues/1059)
- Better LearningMaterial API endpoint [\#1040](https://github.com/ilios/ilios/issues/1040)
- Return a numerically indexed array from cget actions controllers [\#1029](https://github.com/ilios/ilios/issues/1029)
- Offerings belonging to deleted sessions are showing up in the user api instructed offering list [\#1028](https://github.com/ilios/ilios/issues/1028)
- Add Learning Materials and other Details to ICS Feed [\#1020](https://github.com/ilios/ilios/issues/1020)
- Learning Material Download [\#1019](https://github.com/ilios/ilios/issues/1019)
- API for PendingUserUpdates [\#1016](https://github.com/ilios/ilios/issues/1016)
- Rename ucUid in user api to campusId [\#1011](https://github.com/ilios/ilios/issues/1011)
- Problem with access to users in a learner group [\#1004](https://github.com/ilios/ilios/issues/1004)
- Session-\>sessionDescription is in correct [\#1002](https://github.com/ilios/ilios/issues/1002)
- sync ilios2 and ilios3 - third iteration. [\#997](https://github.com/ilios/ilios/issues/997)
- Add Curiculum Inventory Generation Tools [\#995](https://github.com/ilios/ilios/issues/995)
- Add auditlog export console command to write audit logs to disk [\#994](https://github.com/ilios/ilios/issues/994)
- Add teaching reminders email console command [\#993](https://github.com/ilios/ilios/issues/993)
- Add change alerts email console command [\#992](https://github.com/ilios/ilios/issues/992)
- Add user sync command [\#991](https://github.com/ilios/ilios/issues/991)
- add 'create user one' command [\#986](https://github.com/ilios/ilios/issues/986)
- Unable to publish course \(and maybe anything else\) [\#984](https://github.com/ilios/ilios/issues/984)
- Unable to upload learning material file [\#983](https://github.com/ilios/ilios/issues/983)
- Allow authentication via LDAP [\#979](https://github.com/ilios/ilios/issues/979)
- Enable doctrine cache in production? [\#976](https://github.com/ilios/ilios/issues/976)
- Incorrect class name for Schoolevents API docs [\#972](https://github.com/ilios/ilios/issues/972)
- Remove API\_Key entity [\#966](https://github.com/ilios/ilios/issues/966)
- Remove CI\_Session Entity [\#965](https://github.com/ilios/ilios/issues/965)
- Set the administrator for new publish events [\#943](https://github.com/ilios/ilios/issues/943)
- Soft delete collections are returned with original keys [\#941](https://github.com/ilios/ilios/issues/941)
- ucUid is still missing in the `users` api response. [\#939](https://github.com/ilios/ilios/issues/939)
- Getting 500 Internal Server Error with the users api [\#938](https://github.com/ilios/ilios/issues/938)
- Remove MeSHUserSelection Concept [\#935](https://github.com/ilios/ilios/issues/935)
- Audit Some Of The Things [\#934](https://github.com/ilios/ilios/issues/934)
- Remove Instruction Hours concept [\#933](https://github.com/ilios/ilios/issues/933)
- Remove Recurring Event Concept [\#932](https://github.com/ilios/ilios/issues/932)
- Create school events api endpoint [\#927](https://github.com/ilios/ilios/issues/927)
- add foreign key referencing `session:session\_id` to `ilm\_session\_facet` table [\#925](https://github.com/ilios/ilios/issues/925)
- consolidate owningSchool, primarySchool and school [\#922](https://github.com/ilios/ilios/issues/922)
- Token implementation partially broken. [\#921](https://github.com/ilios/ilios/issues/921)
- Unable to Move Learner Between Groups - Timeout - 500 error  [\#918](https://github.com/ilios/ilios/issues/918)
- respond to requests passing expired auth token with 401 error code [\#904](https://github.com/ilios/ilios/issues/904)
- Create data loading fixtures [\#892](https://github.com/ilios/ilios/issues/892)
- Make Recurring Event read only [\#890](https://github.com/ilios/ilios/issues/890)
- Create ingestion exception controller [\#889](https://github.com/ilios/ilios/issues/889)
- Allow users to expire issued tokens [\#886](https://github.com/ilios/ilios/issues/886)
- Create /auth/newtoken endpoint [\#885](https://github.com/ilios/ilios/issues/885)
- Remove stored procedures [\#884](https://github.com/ilios/ilios/issues/884)
- IlmSessionFacet should relate to a single session [\#868](https://github.com/ilios/ilios/issues/868)
- Remove LearningMaterial inheritance [\#859](https://github.com/ilios/ilios/issues/859)
- Deprecate publish\_event table\_name and table\_row\_id columns  [\#857](https://github.com/ilios/ilios/issues/857)
- Unable to add groups to ILM [\#850](https://github.com/ilios/ilios/issues/850)
- DataLoaders implementation [\#814](https://github.com/ilios/ilios/issues/814)
- Puppet Provisioner is broken [\#813](https://github.com/ilios/ilios/issues/813)
- Add query term to mesh descriptor endpoint [\#811](https://github.com/ilios/ilios/issues/811)
- Add notes to session/course learning material endpoint [\#810](https://github.com/ilios/ilios/issues/810)
- Need special handling for DateTime filtering [\#806](https://github.com/ilios/ilios/issues/806)
- Fix year endpoints [\#776](https://github.com/ilios/ilios/issues/776)
- MeSH Endpoints [\#775](https://github.com/ilios/ilios/issues/775)
- Change session -\> sessionLearningMaterial to learningMaterial [\#766](https://github.com/ilios/ilios/issues/766)
- Fix session description endpoint [\#765](https://github.com/ilios/ilios/issues/765)
- Add Validators for all Entities [\#761](https://github.com/ilios/ilios/issues/761)
- API Test Coverage [\#760](https://github.com/ilios/ilios/issues/760)
- Walk endpoints [\#759](https://github.com/ilios/ilios/issues/759)
- Serve ember app from symfony [\#758](https://github.com/ilios/ilios/issues/758)
- Add Authentication [\#757](https://github.com/ilios/ilios/issues/757)
- User role endpoint title [\#756](https://github.com/ilios/ilios/issues/756)
- Session Endpoint [\#755](https://github.com/ilios/ilios/issues/755)
- Change ILMSessionFacet to ILMSession [\#754](https://github.com/ilios/ilios/issues/754)
- Change all timestamp fields [\#753](https://github.com/ilios/ilios/issues/753)
- Add initial doctrine migration [\#748](https://github.com/ilios/ilios/issues/748)
- Change discipline to topic [\#739](https://github.com/ilios/ilios/issues/739)
- Add searchTerms to user api [\#738](https://github.com/ilios/ilios/issues/738)

**Merged pull requests:**

- tagging the 3.0.0-rc1 release [\#1123](https://github.com/ilios/ilios/pull/1123) ([thecoolestguy](https://github.com/thecoolestguy))
- Update Libraries [\#1121](https://github.com/ilios/ilios/pull/1121) ([jrjohnson](https://github.com/jrjohnson))
- Limit courses correctly when multiple filters are called [\#1115](https://github.com/ilios/ilios/pull/1115) ([jrjohnson](https://github.com/jrjohnson))
- Handle course filter params as an array [\#1113](https://github.com/ilios/ilios/pull/1113) ([jrjohnson](https://github.com/jrjohnson))
- Filter course by deep and interesting things [\#1111](https://github.com/ilios/ilios/pull/1111) ([jrjohnson](https://github.com/jrjohnson))
- Add /errors endpoint for capturing errors from frontend [\#1110](https://github.com/ilios/ilios/pull/1110) ([jrjohnson](https://github.com/jrjohnson))
- lm::link needs to be a string, but not necessarily an URL. [\#1109](https://github.com/ilios/ilios/pull/1109) ([stopfstedt](https://github.com/stopfstedt))
- Match description in learning material search [\#1107](https://github.com/ilios/ilios/pull/1107) ([jrjohnson](https://github.com/jrjohnson))
- Remove unnecessary API fields from highly trafficked endpoints [\#1106](https://github.com/ilios/ilios/pull/1106) ([jrjohnson](https://github.com/jrjohnson))
- Add prepositionalObjectTableRowId to ReportType [\#1105](https://github.com/ilios/ilios/pull/1105) ([jrjohnson](https://github.com/jrjohnson))
- broke up tests. [\#1104](https://github.com/ilios/ilios/pull/1104) ([stopfstedt](https://github.com/stopfstedt))
- Allow users to access the groups they belong to [\#1099](https://github.com/ilios/ilios/pull/1099) ([jrjohnson](https://github.com/jrjohnson))
- Add /auth/logout route [\#1097](https://github.com/ilios/ilios/pull/1097) ([jrjohnson](https://github.com/jrjohnson))
- Return 200 when user does not have an account [\#1096](https://github.com/ilios/ilios/pull/1096) ([jrjohnson](https://github.com/jrjohnson))
- Add endDate to ILMs 15 minutes after dueDate [\#1095](https://github.com/ilios/ilios/pull/1095) ([jrjohnson](https://github.com/jrjohnson))
- have PHPUnit stop on first failure by default. [\#1094](https://github.com/ilios/ilios/pull/1094) ([stopfstedt](https://github.com/stopfstedt))
- makes program title required and short title optional [\#1093](https://github.com/ilios/ilios/pull/1093) ([stopfstedt](https://github.com/stopfstedt))
- added additional visibility checks on courses. [\#1092](https://github.com/ilios/ilios/pull/1092) ([stopfstedt](https://github.com/stopfstedt))
- Whitelisted \<u\> element in input sanitation rules. [\#1090](https://github.com/ilios/ilios/pull/1090) ([stopfstedt](https://github.com/stopfstedt))
- added API endpoint for permissions [\#1088](https://github.com/ilios/ilios/pull/1088) ([stopfstedt](https://github.com/stopfstedt))
- remove soft deletes [\#1087](https://github.com/ilios/ilios/pull/1087) ([stopfstedt](https://github.com/stopfstedt))
- simplified learning material voter rules [\#1086](https://github.com/ilios/ilios/pull/1086) ([stopfstedt](https://github.com/stopfstedt))
- replace getter-chains with null-safe getter methods [\#1076](https://github.com/ilios/ilios/pull/1076) ([stopfstedt](https://github.com/stopfstedt))
- add filtering by role to user search [\#1074](https://github.com/ilios/ilios/pull/1074) ([stopfstedt](https://github.com/stopfstedt))
- Handle inverse side of manyToMany relationships [\#1073](https://github.com/ilios/ilios/pull/1073) ([jrjohnson](https://github.com/jrjohnson))
- More schools for a user [\#1058](https://github.com/ilios/ilios/pull/1058) ([jrjohnson](https://github.com/jrjohnson))
- Add a q \(query\) to get learningmaterials [\#1055](https://github.com/ilios/ilios/pull/1055) ([jrjohnson](https://github.com/jrjohnson))
- fix mesh descriptor search [\#1050](https://github.com/ilios/ilios/pull/1050) ([stopfstedt](https://github.com/stopfstedt))

## [v3.0.0-beta9](https://github.com/ilios/ilios/tree/v3.0.0-beta9) (2015-10-27)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta8...v3.0.0-beta9)

**Implemented enhancements:**

- ability to pass expiration date to refresh token API endpoint [\#917](https://github.com/ilios/ilios/issues/917)

**Closed issues:**

- Can't save course objective [\#1068](https://github.com/ilios/ilios/issues/1068)
- ILM Instructional Hours does not allow for fractional time [\#1066](https://github.com/ilios/ilios/issues/1066)
- Run Symfony deprecation detector on Ilios codebase [\#1056](https://github.com/ilios/ilios/issues/1056)
- Learner Group Mgt edit fails with 403 [\#1049](https://github.com/ilios/ilios/issues/1049)
- Learner Group Mgt Bulk edit fails with 403 [\#1048](https://github.com/ilios/ilios/issues/1048)
- Session Offerings Get Removed After Changing Session Data [\#1039](https://github.com/ilios/ilios/issues/1039)
- Navigation to Courses and Sessions From All Events Calendar Very Slow \(if Eventual\) [\#996](https://github.com/ilios/ilios/issues/996)
- Can we declare support for only a JSON API? [\#812](https://github.com/ilios/ilios/issues/812)
- Add PHP pre-commit hooks [\#769](https://github.com/ilios/ilios/issues/769)

**Merged pull requests:**

- v3.0.0-beta9 release [\#1071](https://github.com/ilios/ilios/pull/1071) ([thecoolestguy](https://github.com/thecoolestguy))
- Allow fractional ILM hours [\#1067](https://github.com/ilios/ilios/pull/1067) ([jrjohnson](https://github.com/jrjohnson))
- Allows offerings to save publishEvents [\#1064](https://github.com/ilios/ilios/pull/1064) ([jrjohnson](https://github.com/jrjohnson))
- Don't allow saving of the many side on a one to many relationship [\#1061](https://github.com/ilios/ilios/pull/1061) ([jrjohnson](https://github.com/jrjohnson))
- replaced deprecated methods [\#1060](https://github.com/ilios/ilios/pull/1060) ([stopfstedt](https://github.com/stopfstedt))
- sanitize form input. [\#1053](https://github.com/ilios/ilios/pull/1053) ([stopfstedt](https://github.com/stopfstedt))
- API improvements [\#1052](https://github.com/ilios/ilios/pull/1052) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0-beta8](https://github.com/ilios/ilios/tree/v3.0.0-beta8) (2015-10-13)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta7...v3.0.0-beta8)

**Closed issues:**

- Typo in ember.debug.js [\#1045](https://github.com/ilios/ilios/issues/1045)

**Merged pull requests:**

- V3.0.0-beta8 Release [\#1046](https://github.com/ilios/ilios/pull/1046) ([thecoolestguy](https://github.com/thecoolestguy))
- added generation/update of offering change alerts [\#1044](https://github.com/ilios/ilios/pull/1044) ([stopfstedt](https://github.com/stopfstedt))
- Remove the hosts matcher from CORS options [\#1043](https://github.com/ilios/ilios/pull/1043) ([jrjohnson](https://github.com/jrjohnson))
- Fixes for program, user and learningmaterial endpoints [\#1042](https://github.com/ilios/ilios/pull/1042) ([jrjohnson](https://github.com/jrjohnson))
- change alerts command. [\#1038](https://github.com/ilios/ilios/pull/1038) ([stopfstedt](https://github.com/stopfstedt))
- Fix commands and broken build [\#1036](https://github.com/ilios/ilios/pull/1036) ([jrjohnson](https://github.com/jrjohnson))
- added teaching reminder command [\#1035](https://github.com/ilios/ilios/pull/1035) ([stopfstedt](https://github.com/stopfstedt))

## [v3.0.0-beta7](https://github.com/ilios/ilios/tree/v3.0.0-beta7) (2015-09-25)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta6...v3.0.0-beta7)

**Closed issues:**

- Unnecessary Blue Line On Dashboard [\#1026](https://github.com/ilios/ilios/issues/1026)

**Merged pull requests:**

- updated changelog for release v3.0.0-beta7 [\#1034](https://github.com/ilios/ilios/pull/1034) ([thecoolestguy](https://github.com/thecoolestguy))
- Handle deleted offerings in the user API [\#1033](https://github.com/ilios/ilios/pull/1033) ([jrjohnson](https://github.com/jrjohnson))
- Add details to ics feed [\#1032](https://github.com/ilios/ilios/pull/1032) ([jrjohnson](https://github.com/jrjohnson))
- Entity managers cleanup [\#1031](https://github.com/ilios/ilios/pull/1031) ([stopfstedt](https://github.com/stopfstedt))
- Fixes for API Issues [\#1030](https://github.com/ilios/ilios/pull/1030) ([jrjohnson](https://github.com/jrjohnson))
- Update vagrant provisioner [\#1027](https://github.com/ilios/ilios/pull/1027) ([jrjohnson](https://github.com/jrjohnson))
- A more open CORS policy for the entire backend [\#1025](https://github.com/ilios/ilios/pull/1025) ([jrjohnson](https://github.com/jrjohnson))
- fixed typo in error message. removed server var dump from error message. [\#1024](https://github.com/ilios/ilios/pull/1024) ([stopfstedt](https://github.com/stopfstedt))
- declared getters/setters in entity interfaces. [\#1023](https://github.com/ilios/ilios/pull/1023) ([stopfstedt](https://github.com/stopfstedt))
- Add PendingUserUpdate API endpoint [\#1022](https://github.com/ilios/ilios/pull/1022) ([jrjohnson](https://github.com/jrjohnson))
- Learning Material Downloads [\#1021](https://github.com/ilios/ilios/pull/1021) ([jrjohnson](https://github.com/jrjohnson))
- Add ICS Feed [\#1018](https://github.com/ilios/ilios/pull/1018) ([jrjohnson](https://github.com/jrjohnson))
- port curriculum inventory exporter to ilios3 [\#1017](https://github.com/ilios/ilios/pull/1017) ([stopfstedt](https://github.com/stopfstedt))
- Fix issue with production install [\#1015](https://github.com/ilios/ilios/pull/1015) ([jrjohnson](https://github.com/jrjohnson))
- User Management Commands [\#1013](https://github.com/ilios/ilios/pull/1013) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0-beta6](https://github.com/ilios/ilios/tree/v3.0.0-beta6) (2015-09-11)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta5...v3.0.0-beta6)

**Merged pull requests:**

- Prepare for v3.0.0-beta6 release [\#1014](https://github.com/ilios/ilios/pull/1014) ([jrjohnson](https://github.com/jrjohnson))
- upped VM and PHP memory limits [\#1012](https://github.com/ilios/ilios/pull/1012) ([stopfstedt](https://github.com/stopfstedt))
- import MeSH default data population [\#1009](https://github.com/ilios/ilios/pull/1009) ([stopfstedt](https://github.com/stopfstedt))
- Ensure read access to learners with a secondary school [\#1008](https://github.com/ilios/ilios/pull/1008) ([jrjohnson](https://github.com/jrjohnson))
- JWT Expiration and Management [\#1007](https://github.com/ilios/ilios/pull/1007) ([jrjohnson](https://github.com/jrjohnson))
- Add missing properties to userType [\#1006](https://github.com/ilios/ilios/pull/1006) ([jrjohnson](https://github.com/jrjohnson))
- 994 auditlog export [\#1005](https://github.com/ilios/ilios/pull/1005) ([stopfstedt](https://github.com/stopfstedt))
- Fix session and sessionDescription API relationship [\#1003](https://github.com/ilios/ilios/pull/1003) ([jrjohnson](https://github.com/jrjohnson))
- More database sync changes [\#1001](https://github.com/ilios/ilios/pull/1001) ([jrjohnson](https://github.com/jrjohnson))
- Better authentication error handling [\#1000](https://github.com/ilios/ilios/pull/1000) ([jrjohnson](https://github.com/jrjohnson))
- Fix file upload [\#999](https://github.com/ilios/ilios/pull/999) ([jrjohnson](https://github.com/jrjohnson))
- Add LDAP authentication option [\#998](https://github.com/ilios/ilios/pull/998) ([jrjohnson](https://github.com/jrjohnson))
- Initial Migration and Update SQL alignment [\#990](https://github.com/ilios/ilios/pull/990) ([jrjohnson](https://github.com/jrjohnson))
- port 'install user zero' shell script to SF2 command [\#989](https://github.com/ilios/ilios/pull/989) ([stopfstedt](https://github.com/stopfstedt))
- Enable APC cache in production [\#988](https://github.com/ilios/ilios/pull/988) ([jrjohnson](https://github.com/jrjohnson))
- The MeSH Universe Revealed [\#987](https://github.com/ilios/ilios/pull/987) ([jrjohnson](https://github.com/jrjohnson))
- renamed discipline to topic [\#985](https://github.com/ilios/ilios/pull/985) ([stopfstedt](https://github.com/stopfstedt))
- import default data population [\#982](https://github.com/ilios/ilios/pull/982) ([stopfstedt](https://github.com/stopfstedt))
- Handle dates correctly in the filters [\#981](https://github.com/ilios/ilios/pull/981) ([jrjohnson](https://github.com/jrjohnson))
- Standardize sessionLearningMaterial and courseLearningMaterial [\#980](https://github.com/ilios/ilios/pull/980) ([jrjohnson](https://github.com/jrjohnson))
- Improved schema for ILMSession [\#978](https://github.com/ilios/ilios/pull/978) ([jrjohnson](https://github.com/jrjohnson))
- defined setter/getter methods for curr inv report in program interface. [\#977](https://github.com/ilios/ilios/pull/977) ([stopfstedt](https://github.com/stopfstedt))
- Change educational year to academic year [\#970](https://github.com/ilios/ilios/pull/970) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0-beta5](https://github.com/ilios/ilios/tree/v3.0.0-beta5) (2015-08-25)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta4...v3.0.0-beta5)

**Closed issues:**

- add auto-incrementing primary key to ingestion\_exception table [\#963](https://github.com/ilios/ilios/issues/963)
- migration: update script and initial doctrine migration produce different schemas [\#959](https://github.com/ilios/ilios/issues/959)
- implement DeletableEntity interface and trait [\#956](https://github.com/ilios/ilios/issues/956)
- re-define recurring event to offering relationship [\#929](https://github.com/ilios/ilios/issues/929)
- Ghost Image of Suggested \(Auto-Generated\) Course ID missing in new Ilios version [\#794](https://github.com/ilios/ilios/issues/794)

**Merged pull requests:**

- Fix annotation for Schoolevents endpoint [\#975](https://github.com/ilios/ilios/pull/975) ([jrjohnson](https://github.com/jrjohnson))
- Clean up dependencies [\#974](https://github.com/ilios/ilios/pull/974) ([jrjohnson](https://github.com/jrjohnson))
- Add notes to course and session learning materials [\#973](https://github.com/ilios/ilios/pull/973) ([jrjohnson](https://github.com/jrjohnson))
- implements "deletable entity" interface and trait  [\#971](https://github.com/ilios/ilios/pull/971) ([stopfstedt](https://github.com/stopfstedt))
- Log changes to entities using a doctrine event [\#968](https://github.com/ilios/ilios/pull/968) ([jrjohnson](https://github.com/jrjohnson))
- removes API Key and CI Sessions from db schema and code. [\#967](https://github.com/ilios/ilios/pull/967) ([stopfstedt](https://github.com/stopfstedt))
- vagrant: increase PHP memory limit for CLI [\#964](https://github.com/ilios/ilios/pull/964) ([stopfstedt](https://github.com/stopfstedt))
- created read-only ingestion exception controller [\#962](https://github.com/ilios/ilios/pull/962) ([stopfstedt](https://github.com/stopfstedt))
- removes MeSH user selection concept [\#961](https://github.com/ilios/ilios/pull/961) ([stopfstedt](https://github.com/stopfstedt))
- aligned schema upgrade script with first doctrine migration. [\#960](https://github.com/ilios/ilios/pull/960) ([stopfstedt](https://github.com/stopfstedt))
- Renamed owningSchool and primarySchool properties to school. [\#957](https://github.com/ilios/ilios/pull/957) ([stopfstedt](https://github.com/stopfstedt))
- fixed school id comparison [\#955](https://github.com/ilios/ilios/pull/955) ([stopfstedt](https://github.com/stopfstedt))
- removed broken code from jwt token class [\#952](https://github.com/ilios/ilios/pull/952) ([stopfstedt](https://github.com/stopfstedt))
- set admin user on publish event creation. [\#951](https://github.com/ilios/ilios/pull/951) ([stopfstedt](https://github.com/stopfstedt))
- purged instruction hours and recurring events from ilios [\#950](https://github.com/ilios/ilios/pull/950) ([stopfstedt](https://github.com/stopfstedt))
- User Authorization [\#948](https://github.com/ilios/ilios/pull/948) ([stopfstedt](https://github.com/stopfstedt))
- deprecated publish event components [\#946](https://github.com/ilios/ilios/pull/946) ([stopfstedt](https://github.com/stopfstedt))
- Stop deactivating new student accounts [\#940](https://github.com/ilios/ilios/pull/940) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0-beta4](https://github.com/ilios/ilios/tree/v3.0.0-beta4) (2015-08-10)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta3...v3.0.0-beta4)

**Implemented enhancements:**

- refreshing token should honor TTL of given token [\#913](https://github.com/ilios/ilios/issues/913)
- create custom learning material validator class. [\#897](https://github.com/ilios/ilios/issues/897)

**Closed issues:**

- Finish user events endpoint [\#920](https://github.com/ilios/ilios/issues/920)
- API: auth/refreshToken broken [\#910](https://github.com/ilios/ilios/issues/910)
- Trying to Load Up Learner Groups Ember Error [\#909](https://github.com/ilios/ilios/issues/909)
- Add test for user events API [\#907](https://github.com/ilios/ilios/issues/907)
- Use production builds for production [\#905](https://github.com/ilios/ilios/issues/905)
- Add an endpoint for uploading files [\#895](https://github.com/ilios/ilios/issues/895)
- The return parameter name, 'curriculumInventoryInstitution', under the 'schools' api is misspelled as 'curriculumInventoryInsitution' [\#891](https://github.com/ilios/ilios/issues/891)
- Remove PATCH methods [\#888](https://github.com/ilios/ilios/issues/888)
- Authorize all API requests [\#887](https://github.com/ilios/ilios/issues/887)
- Merge report and report\_po tables. [\#879](https://github.com/ilios/ilios/issues/879)
- Add new primary key to session\_description table [\#878](https://github.com/ilios/ilios/issues/878)
- schema change: retrofit curriculum inventory tables with dedicated primary keys [\#876](https://github.com/ilios/ilios/issues/876)
- Apache config error in Vagrant build [\#869](https://github.com/ilios/ilios/issues/869)
- Replace Triggers with Keys or Events [\#866](https://github.com/ilios/ilios/issues/866)
- Demo Cohort Drop-Down Displaying Blanks and Is Not Ordered - Learner Groups [\#862](https://github.com/ilios/ilios/issues/862)
- Change ILMSessionFacet to ILMSession across the board [\#858](https://github.com/ilios/ilios/issues/858)
- Session Description Edit Save Event Not Working [\#854](https://github.com/ilios/ilios/issues/854)
- Add Learning Material \(Any Type\) To Course Doesn't Work [\#851](https://github.com/ilios/ilios/issues/851)
- Unable to add instructors [\#849](https://github.com/ilios/ilios/issues/849)
- Course page load throws error [\#847](https://github.com/ilios/ilios/issues/847)
- MeSH Terms Not Displaying in the Grid But Appear to Have been Saved [\#840](https://github.com/ilios/ilios/issues/840)
- Level is not working properly - Reverts to Previously Saved Value [\#839](https://github.com/ilios/ilios/issues/839)
- Instructor Groups Ilios Menu Item Not Working on Demo Site [\#833](https://github.com/ilios/ilios/issues/833)
- Delete Validation Needed For Learner Groups [\#829](https://github.com/ilios/ilios/issues/829)
- Change SOD support email address in 2.4.x branch [\#815](https://github.com/ilios/ilios/issues/815)
- Identifiable Entity trait setter/getter methods wrongly assume int as only possible datatype [\#799](https://github.com/ilios/ilios/issues/799)
- Consistency with Visibility and Behavior \>\> Courses and Sessions \>\> New [\#797](https://github.com/ilios/ilios/issues/797)
- There is No Way to Close/Undo the Add Cohort Functionality  [\#795](https://github.com/ilios/ilios/issues/795)
- Courses and Sessions \>\> View All / Edit Label - Can We Change It? [\#793](https://github.com/ilios/ilios/issues/793)
- Change External ID label to Course ID \(like in current Ilios version\) [\#792](https://github.com/ilios/ilios/issues/792)
- Instruction Hours not set up for Validation yet [\#783](https://github.com/ilios/ilios/issues/783)
- Test entity validators [\#781](https://github.com/ilios/ilios/issues/781)
- Add \_collectionSize to every endpoint [\#773](https://github.com/ilios/ilios/issues/773)
- Handle deletions correctly [\#745](https://github.com/ilios/ilios/issues/745)

**Merged pull requests:**

- Add ucUid and otherId to users endpoint [\#944](https://github.com/ilios/ilios/pull/944) ([jrjohnson](https://github.com/jrjohnson))
- Improve handling of soft deleted collections [\#942](https://github.com/ilios/ilios/pull/942) ([jrjohnson](https://github.com/jrjohnson))
- Added lockable and archivable entity traits and interfaces [\#936](https://github.com/ilios/ilios/pull/936) ([stopfstedt](https://github.com/stopfstedt))
- Enable doctrine migrations [\#931](https://github.com/ilios/ilios/pull/931) ([jrjohnson](https://github.com/jrjohnson))
- Enable Soft Deletes [\#930](https://github.com/ilios/ilios/pull/930) ([jrjohnson](https://github.com/jrjohnson))
- Add schoolevents api [\#928](https://github.com/ilios/ilios/pull/928) ([jrjohnson](https://github.com/jrjohnson))
- Complete Userevents API [\#926](https://github.com/ilios/ilios/pull/926) ([jrjohnson](https://github.com/jrjohnson))
- DRI: refactoring of Entity Managers [\#919](https://github.com/ilios/ilios/pull/919) ([stopfstedt](https://github.com/stopfstedt))
- refreshing api tokens should honor given TTL [\#916](https://github.com/ilios/ilios/pull/916) ([stopfstedt](https://github.com/stopfstedt))
- reformatted code to appease codesniffler. [\#912](https://github.com/ilios/ilios/pull/912) ([stopfstedt](https://github.com/stopfstedt))
- fixes "refresh token" controller action [\#911](https://github.com/ilios/ilios/pull/911) ([stopfstedt](https://github.com/stopfstedt))
- Fix user event query to match offering entity [\#906](https://github.com/ilios/ilios/pull/906) ([jrjohnson](https://github.com/jrjohnson))
- validate weblink/citation/file LM differently  [\#901](https://github.com/ilios/ilios/pull/901) ([stopfstedt](https://github.com/stopfstedt))
- Fix broken instructor groups controller test [\#900](https://github.com/ilios/ilios/pull/900) ([jrjohnson](https://github.com/jrjohnson))
- fixed typo in code comment. [\#898](https://github.com/ilios/ilios/pull/898) ([stopfstedt](https://github.com/stopfstedt))
- Upload files and save learning materials [\#896](https://github.com/ilios/ilios/pull/896) ([jrjohnson](https://github.com/jrjohnson))
- removes PATCH actions from controllers. [\#894](https://github.com/ilios/ilios/pull/894) ([stopfstedt](https://github.com/stopfstedt))
- Remove remaining triggers, procedures, and functions [\#893](https://github.com/ilios/ilios/pull/893) ([jrjohnson](https://github.com/jrjohnson))
- renamed 'ilm session facet' to 'ilm session' [\#883](https://github.com/ilios/ilios/pull/883) ([stopfstedt](https://github.com/stopfstedt))
- On the path to a complete API... tests for almost everything [\#882](https://github.com/ilios/ilios/pull/882) ([jrjohnson](https://github.com/jrjohnson))
- Remove ReportPoValue entity [\#881](https://github.com/ilios/ilios/pull/881) ([jrjohnson](https://github.com/jrjohnson))
- added new primary key to session\_description table [\#880](https://github.com/ilios/ilios/pull/880) ([stopfstedt](https://github.com/stopfstedt))
- retrofitted curriculum inventory entities with new ids [\#877](https://github.com/ilios/ilios/pull/877) ([stopfstedt](https://github.com/stopfstedt))
- XDebug in Vagrant [\#875](https://github.com/ilios/ilios/pull/875) ([stopfstedt](https://github.com/stopfstedt))
- gitignore phpunit.xml [\#874](https://github.com/ilios/ilios/pull/874) ([stopfstedt](https://github.com/stopfstedt))
- ilios session facet relationship mapping [\#873](https://github.com/ilios/ilios/pull/873) ([stopfstedt](https://github.com/stopfstedt))
- Replace Some ILMSession triggers [\#872](https://github.com/ilios/ilios/pull/872) ([jrjohnson](https://github.com/jrjohnson))
- Better puppet config for apache and shibboleth [\#871](https://github.com/ilios/ilios/pull/871) ([jrjohnson](https://github.com/jrjohnson))
- Update a session when its ILM is updated [\#870](https://github.com/ilios/ilios/pull/870) ([jrjohnson](https://github.com/jrjohnson))
- added INSTALL.md and updated UPGRADE.md files [\#863](https://github.com/ilios/ilios/pull/863) ([thecoolestguy](https://github.com/thecoolestguy))

## [v3.0.0-beta3](https://github.com/ilios/ilios/tree/v3.0.0-beta3) (2015-06-30)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta2...v3.0.0-beta3)

**Merged pull requests:**

- Beta 3 Release Prep [\#865](https://github.com/ilios/ilios/pull/865) ([jrjohnson](https://github.com/jrjohnson))
- Authentication [\#864](https://github.com/ilios/ilios/pull/864) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0-beta2](https://github.com/ilios/ilios/tree/v3.0.0-beta2) (2015-06-20)
[Full Changelog](https://github.com/ilios/ilios/compare/v3.0.0-beta1...v3.0.0-beta2)

**Merged pull requests:**

- Release Preparation [\#861](https://github.com/ilios/ilios/pull/861) ([jrjohnson](https://github.com/jrjohnson))

## [v3.0.0-beta1](https://github.com/ilios/ilios/tree/v3.0.0-beta1) (2015-06-19)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.8...v3.0.0-beta1)

**Closed issues:**

- Unable to Add Instructor To Session Offering [\#855](https://github.com/ilios/ilios/issues/855)
- Session Toggles Return the Error Below When Change Event Occurs and Sometime Require Multiple Clicks [\#852](https://github.com/ilios/ilios/issues/852)
- Unable to modify Session Type [\#848](https://github.com/ilios/ilios/issues/848)
- Add Cohort fails to function [\#846](https://github.com/ilios/ilios/issues/846)
- Learning Materials are not retrieving/displaying “status” or “instructional notes” in the edit form [\#845](https://github.com/ilios/ilios/issues/845)
- Course Director Search Cancel Button Disappears [\#844](https://github.com/ilios/ilios/issues/844)
- Course Director Search Not Returning Correct Results [\#843](https://github.com/ilios/ilios/issues/843)
- Course Director Save Not Happening  [\#842](https://github.com/ilios/ilios/issues/842)
- Manage MeSH Button Does Not Function [\#841](https://github.com/ilios/ilios/issues/841)
- Clerkship Type Not Loading on Demo [\#838](https://github.com/ilios/ilios/issues/838)
- Publish Course Not Working on Demo Server \(API\) [\#837](https://github.com/ilios/ilios/issues/837)
- Save Event Not Working - Select Parent Objective for Course Objective [\#836](https://github.com/ilios/ilios/issues/836)
- Session Type Endpoint Broken [\#835](https://github.com/ilios/ilios/issues/835)
- Save Event For Adding Course Level Objective Not Working On Demo [\#834](https://github.com/ilios/ilios/issues/834)
- Session Edit Not Working on Demo Site \(API Issue?\) [\#832](https://github.com/ilios/ilios/issues/832)
- Add New Course \>\> Suggestion -- Place Add Cohort Higher on the Screen [\#808](https://github.com/ilios/ilios/issues/808)
- MeSH Terms Search / Results Suggestions  [\#796](https://github.com/ilios/ilios/issues/796)
- Add repo-specific .gitattributes file to force unix line-endings for all users [\#787](https://github.com/ilios/ilios/issues/787)
- Vagrant needs to run on Windows -- errors with nfsd [\#779](https://github.com/ilios/ilios/issues/779)
- Change from searchTerm to Q for special search [\#772](https://github.com/ilios/ilios/issues/772)

**Merged pull requests:**

- API fixes from frontend testing [\#860](https://github.com/ilios/ilios/pull/860) ([jrjohnson](https://github.com/jrjohnson))
- More API Tests [\#856](https://github.com/ilios/ilios/pull/856) ([jrjohnson](https://github.com/jrjohnson))
- Fix issue with API relationships [\#853](https://github.com/ilios/ilios/pull/853) ([jrjohnson](https://github.com/jrjohnson))
- Fixes routes and action implementations. Makes resources plural. [\#831](https://github.com/ilios/ilios/pull/831) ([vpassapera](https://github.com/vpassapera))
- Added User Events API endpoint [\#830](https://github.com/ilios/ilios/pull/830) ([jrjohnson](https://github.com/jrjohnson))
- Make the index file location configurable [\#828](https://github.com/ilios/ilios/pull/828) ([jrjohnson](https://github.com/jrjohnson))
- Serve the frontend from symfony [\#827](https://github.com/ilios/ilios/pull/827) ([jrjohnson](https://github.com/jrjohnson))
- Expose additional API data [\#826](https://github.com/ilios/ilios/pull/826) ([jrjohnson](https://github.com/jrjohnson))
- Fixes to generated files to work with controller tests. [\#825](https://github.com/ilios/ilios/pull/825) ([vpassapera](https://github.com/vpassapera))
- Added Some controller tests [\#823](https://github.com/ilios/ilios/pull/823) ([vpassapera](https://github.com/vpassapera))
- Add title to department endpoint [\#817](https://github.com/ilios/ilios/pull/817) ([jrjohnson](https://github.com/jrjohnson))
- changed SOD email to the new one, fixes \#815 [\#816](https://github.com/ilios/ilios/pull/816) ([thecoolestguy](https://github.com/thecoolestguy))
- user/user-role test fixture switcheroo [\#809](https://github.com/ilios/ilios/pull/809) ([stopfstedt](https://github.com/stopfstedt))
- Better API for learning materials [\#807](https://github.com/ilios/ilios/pull/807) ([jrjohnson](https://github.com/jrjohnson))
- Assorted API Improvements [\#804](https://github.com/ilios/ilios/pull/804) ([jrjohnson](https://github.com/jrjohnson))
- Get all the pcrses [\#802](https://github.com/ilios/ilios/pull/802) ([stopfstedt](https://github.com/stopfstedt))
- A few more data loaders and test fixtures [\#801](https://github.com/ilios/ilios/pull/801) ([stopfstedt](https://github.com/stopfstedt))
- id attribute of identifiable entity can be string or int. [\#800](https://github.com/ilios/ilios/pull/800) ([stopfstedt](https://github.com/stopfstedt))
- added equals sign for proper handling [\#791](https://github.com/ilios/ilios/pull/791) ([thecoolestguy](https://github.com/thecoolestguy))
- changed the .gitattributes file to enforce unix line ending on all .sh, .php, and .sql files in the repo [\#788](https://github.com/ilios/ilios/pull/788) ([thecoolestguy](https://github.com/thecoolestguy))
- Fix session creation in API [\#786](https://github.com/ilios/ilios/pull/786) ([jrjohnson](https://github.com/jrjohnson))
- Move php to Puppetfile [\#785](https://github.com/ilios/ilios/pull/785) ([vpassapera](https://github.com/vpassapera))
- Validation tests [\#784](https://github.com/ilios/ilios/pull/784) ([dartajax](https://github.com/dartajax))
- Slightly cleaner test code for the course entity [\#782](https://github.com/ilios/ilios/pull/782) ([jrjohnson](https://github.com/jrjohnson))
- Branch vagrant shared folder config on windows. [\#780](https://github.com/ilios/ilios/pull/780) ([jrjohnson](https://github.com/jrjohnson))
- Entity Annotation Validations [\#778](https://github.com/ilios/ilios/pull/778) ([jrjohnson](https://github.com/jrjohnson))
- Use q= instead of searchTerm= for finding by string [\#774](https://github.com/ilios/ilios/pull/774) ([jrjohnson](https://github.com/jrjohnson))
- Update composer.json [\#771](https://github.com/ilios/ilios/pull/771) ([vpassapera](https://github.com/vpassapera))
- Add description and ID to session description endpoint [\#770](https://github.com/ilios/ilios/pull/770) ([jrjohnson](https://github.com/jrjohnson))
- Use tdn generator and included types for forms [\#768](https://github.com/ilios/ilios/pull/768) ([jrjohnson](https://github.com/jrjohnson))
- Update dependencies to fix issue with tdn/phptype tree [\#767](https://github.com/ilios/ilios/pull/767) ([jrjohnson](https://github.com/jrjohnson))
- Annotations added to Entities [\#764](https://github.com/ilios/ilios/pull/764) ([dartajax](https://github.com/dartajax))
- fixed typo [\#763](https://github.com/ilios/ilios/pull/763) ([stopfstedt](https://github.com/stopfstedt))
- Added coveralls and minor cleanup [\#752](https://github.com/ilios/ilios/pull/752) ([jrjohnson](https://github.com/jrjohnson))
- Userroles dataloader [\#751](https://github.com/ilios/ilios/pull/751) ([stopfstedt](https://github.com/stopfstedt))
- Use custom Types and Transformers to handle data [\#750](https://github.com/ilios/ilios/pull/750) ([jrjohnson](https://github.com/jrjohnson))
- Remove Code Igniter [\#749](https://github.com/ilios/ilios/pull/749) ([jrjohnson](https://github.com/jrjohnson))
- Bring Ilios3 into Master [\#744](https://github.com/ilios/ilios/pull/744) ([jrjohnson](https://github.com/jrjohnson))
- Validation and Tests [\#743](https://github.com/ilios/ilios/pull/743) ([jrjohnson](https://github.com/jrjohnson))
- Trim vagrant mercilessly [\#742](https://github.com/ilios/ilios/pull/742) ([jrjohnson](https://github.com/jrjohnson))
- Add the ability to find users by searchTerm [\#741](https://github.com/ilios/ilios/pull/741) ([jrjohnson](https://github.com/jrjohnson))
- Serialize Dates [\#740](https://github.com/ilios/ilios/pull/740) ([jrjohnson](https://github.com/jrjohnson))
- API Changes [\#737](https://github.com/ilios/ilios/pull/737) ([jrjohnson](https://github.com/jrjohnson))
- Fix API HTML Views [\#736](https://github.com/ilios/ilios/pull/736) ([jrjohnson](https://github.com/jrjohnson))
- REST Api [\#735](https://github.com/ilios/ilios/pull/735) ([vpassapera](https://github.com/vpassapera))
- changed variable reference assignment to support php 5.6 [\#731](https://github.com/ilios/ilios/pull/731) ([thecoolestguy](https://github.com/thecoolestguy))
- Linting [\#727](https://github.com/ilios/ilios/pull/727) ([Trott](https://github.com/Trott))
- updated CHANGELOG.txt, ready for release 2.4.8 [\#726](https://github.com/ilios/ilios/pull/726) ([thecoolestguy](https://github.com/thecoolestguy))

## [v2.4.8](https://github.com/ilios/ilios/tree/v2.4.8) (2014-11-04)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.7...v2.4.8)

**Implemented enhancements:**

- provide option for populating new user groups [\#611](https://github.com/ilios/ilios/issues/611)

**Closed issues:**

- Save Event Not Firing When Adding A User \(Admin Console\) [\#715](https://github.com/ilios/ilios/issues/715)
- MeSH Term Not Updating - Session Objectives [\#664](https://github.com/ilios/ilios/issues/664)

**Merged pull requests:**

- Fix incorrectly dropped procedure [\#724](https://github.com/ilios/ilios/pull/724) ([jrjohnson](https://github.com/jrjohnson))
- Changelog and version update for 2.4.8 [\#723](https://github.com/ilios/ilios/pull/723) ([jrjohnson](https://github.com/jrjohnson))
- removed publish\_event\_id check and styling of in-draft offerings [\#722](https://github.com/ilios/ilios/pull/722) ([thecoolestguy](https://github.com/thecoolestguy))
- 'in-draft' status added for more prominent display [\#718](https://github.com/ilios/ilios/pull/718) ([thecoolestguy](https://github.com/thecoolestguy))
- Only save former student role for existing users [\#716](https://github.com/ilios/ilios/pull/716) ([jrjohnson](https://github.com/jrjohnson))
- 'scheduled' session icon color change [\#714](https://github.com/ilios/ilios/pull/714) ([thecoolestguy](https://github.com/thecoolestguy))
- Allow learner groups to be created empty [\#713](https://github.com/ilios/ilios/pull/713) ([jrjohnson](https://github.com/jrjohnson))

## [v2.4.7](https://github.com/ilios/ilios/tree/v2.4.7) (2014-10-14)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.6...v2.4.7)

**Implemented enhancements:**

- calendar download for instructor/director level users [\#221](https://github.com/ilios/ilios/issues/221)
- Special Cron Job for Syncing Users [\#677](https://github.com/ilios/ilios/issues/677)
- Identifying students once they have graduated as "graduates" [\#327](https://github.com/ilios/ilios/issues/327)

**Closed issues:**

- Extend Calendar Feed time period [\#708](https://github.com/ilios/ilios/issues/708)
- Add MeSH\(x\) Label Not Displaying Correct Total \(x\) - Session - Objective [\#666](https://github.com/ilios/ilios/issues/666)
- Disallow NULL password value for setups using anonymous LDAP binds [\#645](https://github.com/ilios/ilios/issues/645)
- Show all associated student groups filter fails [\#636](https://github.com/ilios/ilios/issues/636)
- "Filing" a course removes it from the calendar search/filter widget [\#576](https://github.com/ilios/ilios/issues/576)

**Merged pull requests:**

- updated version number in version.php [\#711](https://github.com/ilios/ilios/pull/711) ([thecoolestguy](https://github.com/thecoolestguy))
- Changelog for 2.4.7 [\#710](https://github.com/ilios/ilios/pull/710) ([jrjohnson](https://github.com/jrjohnson))
- changed date strings to reflect longer calendar period [\#709](https://github.com/ilios/ilios/pull/709) ([thecoolestguy](https://github.com/thecoolestguy))
- re-pointed learner function calls to the learner functions and not the instructor ones [\#707](https://github.com/ilios/ilios/pull/707) ([thecoolestguy](https://github.com/thecoolestguy))
- 576 course filing removal bug [\#706](https://github.com/ilios/ilios/pull/706) ([thecoolestguy](https://github.com/thecoolestguy))
- Flag Former Students in the sync process [\#697](https://github.com/ilios/ilios/pull/697) ([jrjohnson](https://github.com/jrjohnson))
- Objective mesh term counts properly updating on change. [\#696](https://github.com/ilios/ilios/pull/696) ([thecoolestguy](https://github.com/thecoolestguy))
- Backport Vagrant Updates from I3 [\#691](https://github.com/ilios/ilios/pull/691) ([jrjohnson](https://github.com/jrjohnson))
- Remove the sparse ember fronted [\#690](https://github.com/ilios/ilios/pull/690) ([jrjohnson](https://github.com/jrjohnson))
- Fix Program Entity [\#689](https://github.com/ilios/ilios/pull/689) ([jrjohnson](https://github.com/jrjohnson))
- WIP. system upgrade [\#687](https://github.com/ilios/ilios/pull/687) ([vpassapera](https://github.com/vpassapera))
- Clean up LDAP login [\#685](https://github.com/ilios/ilios/pull/685) ([jrjohnson](https://github.com/jrjohnson))
- Basic Structure and Dashboard [\#683](https://github.com/ilios/ilios/pull/683) ([jrjohnson](https://github.com/jrjohnson))
- Add a cron task to run the user sync process and nothing else. [\#678](https://github.com/ilios/ilios/pull/678) ([jrjohnson](https://github.com/jrjohnson))

## [v2.4.6](https://github.com/ilios/ilios/tree/v2.4.6) (2014-09-02)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.6-rc1...v2.4.6)

**Implemented enhancements:**

- decouple persisting learning materials from sessions/courses [\#205](https://github.com/ilios/ilios/issues/205)

**Closed issues:**

- Make "Final" the Default Status for Uploading Learning Materials to Course and / or Session [\#672](https://github.com/ilios/ilios/issues/672)
- Attempting to add Learning Material to course in Firefox closes dialog and refreshes course [\#668](https://github.com/ilios/ilios/issues/668)
- Add MeSH\(null\) Label Displayed - Learning Materials - Session  [\#665](https://github.com/ilios/ilios/issues/665)
- course dirty state interrupts session description save [\#663](https://github.com/ilios/ilios/issues/663)
- An ugly Error msg is returned when no Email address is contained in shibboleth [\#657](https://github.com/ilios/ilios/issues/657)

**Merged pull requests:**

- fixed session description loss upon session publish in dirty course [\#676](https://github.com/ilios/ilios/pull/676) ([stopfstedt](https://github.com/stopfstedt))
- changed default status selection of learning materials to 'final' \(index '1'\) [\#673](https://github.com/ilios/ilios/pull/673) ([thecoolestguy](https://github.com/thecoolestguy))
- learning material dialog disappears in firefox [\#669](https://github.com/ilios/ilios/pull/669) ([thecoolestguy](https://github.com/thecoolestguy))
- added learningmaterials to session cloning [\#667](https://github.com/ilios/ilios/pull/667) ([thecoolestguy](https://github.com/thecoolestguy))

## [v2.4.6-rc1](https://github.com/ilios/ilios/tree/v2.4.6-rc1) (2014-08-15)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.5...v2.4.6-rc1)

**Implemented enhancements:**

- SQL generated from MeSH parser should use REPLACE syntax [\#304](https://github.com/ilios/ilios/issues/304)
- change output from mesh parser from db to file [\#303](https://github.com/ilios/ilios/issues/303)

**Merged pull requests:**

- Update mesh tables install to 2014 mesh terms. [\#662](https://github.com/ilios/ilios/pull/662) ([jrjohnson](https://github.com/jrjohnson))
- 205 decoupling learning material crud [\#661](https://github.com/ilios/ilios/pull/661) ([thecoolestguy](https://github.com/thecoolestguy))
- Localsauce README tweaks [\#659](https://github.com/ilios/ilios/pull/659) ([stopfstedt](https://github.com/stopfstedt))
- Better shibboleth authentication errors [\#658](https://github.com/ilios/ilios/pull/658) ([jrjohnson](https://github.com/jrjohnson))

## [v2.4.5](https://github.com/ilios/ilios/tree/v2.4.5) (2014-08-08)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.3...v2.4.5)

**Implemented enhancements:**

- Provide method for adjusting "duration" for small group events [\#473](https://github.com/ilios/ilios/issues/473)
- add timestamps to mesh\_\* tables \(MeSH Parser\) [\#302](https://github.com/ilios/ilios/issues/302)

**Closed issues:**

- Unable to upload PPTX files [\#653](https://github.com/ilios/ilios/issues/653)
- Validate curriculum inventory upload report against new corrigenda item [\#649](https://github.com/ilios/ilios/issues/649)
- Error Received While Trying to add MeSH term to Program Year [\#617](https://github.com/ilios/ilios/issues/617)
- Refactor CSV upload / user insert tool for instructor and learner groups [\#561](https://github.com/ilios/ilios/issues/561)
- 403 Forbidden should return 403 not 200 [\#521](https://github.com/ilios/ilios/issues/521)
- remove "GALEN ID" label and update the csv load [\#496](https://github.com/ilios/ilios/issues/496)
- Modify "UC ID" labeling globally to "ID" [\#495](https://github.com/ilios/ilios/issues/495)
- expand instruction in user guide etc. for CI management tools [\#423](https://github.com/ilios/ilios/issues/423)
- update the data population for the PCRS to include AAMC last-minutes changes and addition of "x.99 - OTHER" categories [\#422](https://github.com/ilios/ilios/issues/422)

**Merged pull requests:**

- Allow users to be added with Other ID [\#656](https://github.com/ilios/ilios/pull/656) ([jrjohnson](https://github.com/jrjohnson))
- added uc\_uid mapping to shibboleth authentication [\#655](https://github.com/ilios/ilios/pull/655) ([thecoolestguy](https://github.com/thecoolestguy))
- added 'application/zip' identifier to pptx mime-type's array in mimes.php [\#654](https://github.com/ilios/ilios/pull/654) ([thecoolestguy](https://github.com/thecoolestguy))
- Validate new Learners and Instructors [\#652](https://github.com/ilios/ilios/pull/652) ([jrjohnson](https://github.com/jrjohnson))
- Fix Program Objective Mesh Search [\#651](https://github.com/ilios/ilios/pull/651) ([jrjohnson](https://github.com/jrjohnson))
- Symfony Authenticated through Code Igniter [\#648](https://github.com/ilios/ilios/pull/648) ([jrjohnson](https://github.com/jrjohnson))
- corrected given windows version in code comment. [\#647](https://github.com/ilios/ilios/pull/647) ([stopfstedt](https://github.com/stopfstedt))
- First steps in Ilios 3 [\#646](https://github.com/ilios/ilios/pull/646) ([jrjohnson](https://github.com/jrjohnson))
- UC\_ID and Galen ID references removed [\#644](https://github.com/ilios/ilios/pull/644) ([jrjohnson](https://github.com/jrjohnson))
- Add updated\_at and create\_at to mesh data tables [\#642](https://github.com/ilios/ilios/pull/642) ([jrjohnson](https://github.com/jrjohnson))
- added changes from the re-tagged CodeIgniter 2.2.0 release. [\#638](https://github.com/ilios/ilios/pull/638) ([stopfstedt](https://github.com/stopfstedt))
- Allow session offerings to be counted only once [\#635](https://github.com/ilios/ilios/pull/635) ([jrjohnson](https://github.com/jrjohnson))

## [v2.4.3](https://github.com/ilios/ilios/tree/v2.4.3) (2014-06-20)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.2...v2.4.3)

**Implemented enhancements:**

- Add uc\_uid length limitations to configuration [\#626](https://github.com/ilios/ilios/issues/626)
- Google Analytics [\#429](https://github.com/ilios/ilios/issues/429)

**Closed issues:**

- upgrade to codeigniter 2.2.0 [\#628](https://github.com/ilios/ilios/issues/628)
- Sauce for IE11 [\#461](https://github.com/ilios/ilios/issues/461)
- Sauce for IE9 [\#460](https://github.com/ilios/ilios/issues/460)

**Merged pull requests:**

- Changelog for 2.4.3 [\#634](https://github.com/ilios/ilios/pull/634) ([jrjohnson](https://github.com/jrjohnson))
- Add Google Analytics [\#632](https://github.com/ilios/ilios/pull/632) ([jrjohnson](https://github.com/jrjohnson))
- enclosed uc\_uid length OR statement to group the conditions to be properly evaluated together [\#631](https://github.com/ilios/ilios/pull/631) ([thecoolestguy](https://github.com/thecoolestguy))
- adjusted title/hover message for uc id to be a variable value, see \#626 [\#630](https://github.com/ilios/ilios/pull/630) ([thecoolestguy](https://github.com/thecoolestguy))
- Upgraded to CodeIgniter 2.2.0 [\#629](https://github.com/ilios/ilios/pull/629) ([stopfstedt](https://github.com/stopfstedt))
- Uc uid configuration changes [\#627](https://github.com/ilios/ilios/pull/627) ([thecoolestguy](https://github.com/thecoolestguy))
- Sauce and Travis improvements [\#625](https://github.com/ilios/ilios/pull/625) ([jrjohnson](https://github.com/jrjohnson))
- Timeout LDAP connection after 2 minutes [\#624](https://github.com/ilios/ilios/pull/624) ([jrjohnson](https://github.com/jrjohnson))
- Add ldap error string to exceptions [\#623](https://github.com/ilios/ilios/pull/623) ([jrjohnson](https://github.com/jrjohnson))
- correct minor typos in comments [\#622](https://github.com/ilios/ilios/pull/622) ([Trott](https://github.com/Trott))
- Do background of Behat tests in database [\#621](https://github.com/ilios/ilios/pull/621) ([jrjohnson](https://github.com/jrjohnson))
- Added audit log config to phing build [\#620](https://github.com/ilios/ilios/pull/620) ([jrjohnson](https://github.com/jrjohnson))

## [v2.4.2](https://github.com/ilios/ilios/tree/v2.4.2) (2014-05-21)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.2-prerelease-20140407...v2.4.2)

**Implemented enhancements:**

- Provide ability to configure the time step value for start/end times of calendar events [\#595](https://github.com/ilios/ilios/issues/595)
- Create script to automate the regular dump of audit information to log [\#424](https://github.com/ilios/ilios/issues/424)

**Closed issues:**

- phpMyAdmin upload file size too small [\#604](https://github.com/ilios/ilios/issues/604)
- Apostrophes in session\_type titles not being escaped on course model load [\#571](https://github.com/ilios/ilios/issues/571)
- COPYRIGHT file conflicts with LICENSE file [\#558](https://github.com/ilios/ilios/issues/558)
- Streamline Welcome Screen [\#542](https://github.com/ilios/ilios/issues/542)
- Use single email value from 'mail' attribute when multiple values are returned by Shibboleth IDp  [\#614](https://github.com/ilios/ilios/issues/614)
- Public access to vagrant vm [\#568](https://github.com/ilios/ilios/issues/568)
- Do we need to encrypt our sauce token? [\#563](https://github.com/ilios/ilios/issues/563)
- ILM sessions do not sort by date [\#475](https://github.com/ilios/ilios/issues/475)
- Create a CONTRIBUTING.md file [\#468](https://github.com/ilios/ilios/issues/468)
- Replace iWaitSeconds\(\) \(sleep\) with spin\(\) in Behat tests [\#467](https://github.com/ilios/ilios/issues/467)
- be more specific source of user input [\#462](https://github.com/ilios/ilios/issues/462)
- PHPUnit tests should not have database side effects [\#458](https://github.com/ilios/ilios/issues/458)
- behat step tweaks [\#457](https://github.com/ilios/ilios/issues/457)
- Jasmine Unit Tests for ilios\_ui\_rte.js [\#449](https://github.com/ilios/ilios/issues/449)
- Jasmine Unit Tests for ilios\_dom.js [\#448](https://github.com/ilios/ilios/issues/448)
- Script/automate setup and run for behat tests [\#445](https://github.com/ilios/ilios/issues/445)
- Update \(Create?\) High-Level Architecture Document [\#444](https://github.com/ilios/ilios/issues/444)

**Merged pull requests:**

- Temporarily disable Save All Draft button [\#610](https://github.com/ilios/ilios/pull/610) ([jrjohnson](https://github.com/jrjohnson))

## [v2.4.2-prerelease-20140407](https://github.com/ilios/ilios/tree/v2.4.2-prerelease-20140407) (2014-04-07)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.1...v2.4.2-prerelease-20140407)

**Closed issues:**

- Remove final instance of abominable deepCloneAssociativeArray\(\) [\#548](https://github.com/ilios/ilios/issues/548)

## [v2.4.1](https://github.com/ilios/ilios/tree/v2.4.1) (2014-04-04)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.4.0...v2.4.1)

**Closed issues:**

- calendar API feed URL must be 116 characters or less [\#512](https://github.com/ilios/ilios/issues/512)
- death to "createContentContainerMarkup\(\)" [\#510](https://github.com/ilios/ilios/issues/510)
- Change in COURSE DIRECTOR or COURSE MESH TERMS causes unlinking of course objectives [\#545](https://github.com/ilios/ilios/issues/545)
- MyReports fails for instructor search [\#529](https://github.com/ilios/ilios/issues/529)
- learning\_material::token CHAR -\> VARCHAR [\#519](https://github.com/ilios/ilios/issues/519)
- Calendar feed URL length can be too long for use in Google Calendar [\#513](https://github.com/ilios/ilios/issues/513)
- Run Jasmine tests on CI server [\#464](https://github.com/ilios/ilios/issues/464)
- Documentation of security considerations for learning materials [\#414](https://github.com/ilios/ilios/issues/414)

## [v2.4.0](https://github.com/ilios/ilios/tree/v2.4.0) (2014-02-20)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.3.2...v2.4.0)

**Implemented enhancements:**

- update migration scripts to include data population for CI tools [\#433](https://github.com/ilios/ilios/issues/433)
- Warn users that their calendar feed should be treated like a password [\#397](https://github.com/ilios/ilios/issues/397)
- Inability to access learning materials in shibboleth enabled systems [\#380](https://github.com/ilios/ilios/issues/380)
- rig up behat tests to CI process [\#342](https://github.com/ilios/ilios/issues/342)
- streamline auditing system [\#337](https://github.com/ilios/ilios/issues/337)
- convert cucumber tests to behat [\#336](https://github.com/ilios/ilios/issues/336)
- de-ajaxify the login "form" [\#335](https://github.com/ilios/ilios/issues/335)

**Closed issues:**

- Get behat tests to work in Travis [\#456](https://github.com/ilios/ilios/issues/456)
- behat tests for \#199 [\#450](https://github.com/ilios/ilios/issues/450)
- Sauce Labs or other integration for behat tests [\#446](https://github.com/ilios/ilios/issues/446)
- Topics display list requires page refresh to show additions [\#442](https://github.com/ilios/ilios/issues/442)
- enable behat tests against vagrant [\#394](https://github.com/ilios/ilios/issues/394)
- vagrant up does not work with vagrant 1.4.2 [\#392](https://github.com/ilios/ilios/issues/392)
- 'required' in CI Export for sessions [\#375](https://github.com/ilios/ilios/issues/375)
- ilios.utilities.htmlEntities\(\) double encodes " [\#369](https://github.com/ilios/ilios/issues/369)
- eliminate ilios.lang.startsWith\(\) function [\#361](https://github.com/ilios/ilios/issues/361)
- eliminate ilios.lang.trim\(\) function [\#360](https://github.com/ilios/ilios/issues/360)
- eliminate ilios.lang.endsWith\(\) function [\#359](https://github.com/ilios/ilios/issues/359)
- DB install instructions incomplete [\#354](https://github.com/ilios/ilios/issues/354)
- move webcal to webcals [\#348](https://github.com/ilios/ilios/issues/348)
- Notes field in Learning Materials does not alert user if max char length is exceeded. [\#339](https://github.com/ilios/ilios/issues/339)

## [v2.3.2](https://github.com/ilios/ilios/tree/v2.3.2) (2013-12-10)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.3.1...v2.3.2)

**Implemented enhancements:**

- replace dashes in inventory report id [\#295](https://github.com/ilios/ilios/issues/295)
- improve topics picker performance [\#291](https://github.com/ilios/ilios/issues/291)
- translation helper methods [\#288](https://github.com/ilios/ilios/issues/288)
- simplify translation api [\#286](https://github.com/ilios/ilios/issues/286)
- associate each learner group with its "owning" cohort [\#214](https://github.com/ilios/ilios/issues/214)
- refactor user/user-group join tables [\#211](https://github.com/ilios/ilios/issues/211)

**Closed issues:**

- unlink competencies from PCRS [\#268](https://github.com/ilios/ilios/issues/268)
- IE conditional classes not needed [\#262](https://github.com/ilios/ilios/issues/262)
- recurring events tool creates additional offerings when used with "create offerings by group" feature [\#275](https://github.com/ilios/ilios/issues/275)
- mesh picker for my reports not functioning [\#260](https://github.com/ilios/ilios/issues/260)

## [v2.3.1](https://github.com/ilios/ilios/tree/v2.3.1) (2013-11-01)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.3...v2.3.1)

**Implemented enhancements:**

- modify display for course names/IDs in calendar search [\#196](https://github.com/ilios/ilios/issues/196)

**Closed issues:**

- lastest phing version fails in deploy-prepare task [\#248](https://github.com/ilios/ilios/issues/248)
- test [\#192](https://github.com/ilios/ilios/issues/192)
- CI business rules validations [\#246](https://github.com/ilios/ilios/issues/246)
- character encoding issue with curr. inv. export [\#241](https://github.com/ilios/ilios/issues/241)
- category element not properly namespaced in curr. inv. report [\#240](https://github.com/ilios/ilios/issues/240)
- curriculum inventory export fails validation [\#239](https://github.com/ilios/ilios/issues/239)
- External Course ID does not get cloned during course rollover [\#229](https://github.com/ilios/ilios/issues/229)
- relative paths in yui-widgets.css stylesheet wrong [\#226](https://github.com/ilios/ilios/issues/226)
- Curric Inventory Manager Date Range Display [\#225](https://github.com/ilios/ilios/issues/225)
- change "no sessions found" label for course when loading sessions [\#203](https://github.com/ilios/ilios/issues/203)
- "students requiring assignment" flag does not refresh properly [\#202](https://github.com/ilios/ilios/issues/202)
- no dirty state alert for instructor groups page [\#201](https://github.com/ilios/ilios/issues/201)
- no dirty state alert for learner groups page [\#200](https://github.com/ilios/ilios/issues/200)
- character encoding issue with CSV upload [\#198](https://github.com/ilios/ilios/issues/198)
- offering does not display in student search if published with no student group associations [\#195](https://github.com/ilios/ilios/issues/195)
- recurring events display does not load correctly on event creation [\#193](https://github.com/ilios/ilios/issues/193)

**Merged pull requests:**

- various bug fixes to curriculum inventory export  [\#238](https://github.com/ilios/ilios/pull/238) ([stopfstedt](https://github.com/stopfstedt))
- footer shouldn't overlap other items just because I change window size... [\#191](https://github.com/ilios/ilios/pull/191) ([Trott](https://github.com/Trott))
- Make appalling inline CSS abomination marginally less appalling [\#190](https://github.com/ilios/ilios/pull/190) ([Trott](https://github.com/Trott))

## [v2.3](https://github.com/ilios/ilios/tree/v2.3) (2013-09-23)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.2.2...v2.3)

## [v2.2.2](https://github.com/ilios/ilios/tree/v2.2.2) (2013-07-16)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.2.1...v2.2.2)

## [v2.2.1](https://github.com/ilios/ilios/tree/v2.2.1) (2013-03-22)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.2...v2.2.1)

## [v2.2](https://github.com/ilios/ilios/tree/v2.2) (2013-03-06)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.1.2...v2.2)

## [v2.1.2](https://github.com/ilios/ilios/tree/v2.1.2) (2012-12-20)
[Full Changelog](https://github.com/ilios/ilios/compare/v2.1.1...v2.1.2)

**Merged pull requests:**

- Admin3072 [\#28](https://github.com/ilios/ilios/pull/28) ([stopfstedt](https://github.com/stopfstedt))
- BUG 2921: reminder alert messages do not capture primary owning school for course [\#27](https://github.com/ilios/ilios/pull/27) ([stopfstedt](https://github.com/stopfstedt))
- reintegrate rb2.1.2 into master [\#26](https://github.com/ilios/ilios/pull/26) ([stopfstedt](https://github.com/stopfstedt))
- updated UCSF/SOM teaching reminder email template. refs \#3023 [\#25](https://github.com/ilios/ilios/pull/25) ([stopfstedt](https://github.com/stopfstedt))
- reintegrate rb2.1.1 into master [\#24](https://github.com/ilios/ilios/pull/24) ([stopfstedt](https://github.com/stopfstedt))
- Feature 3024: display parent competency titles in learner course summary view [\#23](https://github.com/ilios/ilios/pull/23) ([stopfstedt](https://github.com/stopfstedt))
- reintegrate rb2.1.2 into master  [\#22](https://github.com/ilios/ilios/pull/22) ([stopfstedt](https://github.com/stopfstedt))
- added proper sort order to competency dropdown in program objective dialog [\#21](https://github.com/ilios/ilios/pull/21) ([stopfstedt](https://github.com/stopfstedt))
- added download instructions to README file.  [\#20](https://github.com/ilios/ilios/pull/20) ([stopfstedt](https://github.com/stopfstedt))
- added indexes/foreign key constraints for user\_x\_user\_role table [\#19](https://github.com/ilios/ilios/pull/19) ([thecoolestguy](https://github.com/thecoolestguy))
- refactored hardwired path to Shib Logout Service into a config setting. ... [\#18](https://github.com/ilios/ilios/pull/18) ([stopfstedt](https://github.com/stopfstedt))
- modified idle page timeout mechanism to allow for logout url configurati... [\#17](https://github.com/ilios/ilios/pull/17) ([stopfstedt](https://github.com/stopfstedt))
- added rewrite rule to redirect from ilios2.php to ilios.php. refs \#3059 [\#16](https://github.com/ilios/ilios/pull/16) ([stopfstedt](https://github.com/stopfstedt))
- issue 3055: dropped Ilios version number from page titles [\#15](https://github.com/ilios/ilios/pull/15) ([stopfstedt](https://github.com/stopfstedt))
- drop ilios version number from file- and classnames [\#14](https://github.com/ilios/ilios/pull/14) ([stopfstedt](https://github.com/stopfstedt))
- Don't require executable bit set on .sh file [\#13](https://github.com/ilios/ilios/pull/13) ([Trott](https://github.com/Trott))
- Remove alert index/foreign key-creation from 2.1.1 to 2.2 [\#12](https://github.com/ilios/ilios/pull/12) ([thecoolestguy](https://github.com/thecoolestguy))
- feature \#3051: add extracted mesh installation SQL script/remove tarball. [\#11](https://github.com/ilios/ilios/pull/11) ([stopfstedt](https://github.com/stopfstedt))
- trim whitespace from database insertion values [\#8](https://github.com/ilios/ilios/pull/8) ([thecoolestguy](https://github.com/thecoolestguy))
- updated ilios logo [\#7](https://github.com/ilios/ilios/pull/7) ([stopfstedt](https://github.com/stopfstedt))
- rm needed dirs from .gitignore [\#6](https://github.com/ilios/ilios/pull/6) ([Trott](https://github.com/Trott))
- please merge project name changes on landing page [\#5](https://github.com/ilios/ilios/pull/5) ([stopfstedt](https://github.com/stopfstedt))

## [v2.1.1](https://github.com/ilios/ilios/tree/v2.1.1) (2012-12-07)
**Merged pull requests:**

- please merge with rb2.1.1 and master [\#4](https://github.com/ilios/ilios/pull/4) ([stopfstedt](https://github.com/stopfstedt))
- Vagrant/Puppet easy dev/demo install [\#3](https://github.com/ilios/ilios/pull/3) ([Trott](https://github.com/Trott))
- fixed broken SQL INSERT statement. refs \#3037 [\#1](https://github.com/ilios/ilios/pull/1) ([stopfstedt](https://github.com/stopfstedt))



\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*
