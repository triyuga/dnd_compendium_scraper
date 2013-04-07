dnd_compendium_scraper
======================

What is it?:
- PHP code to extract fields from DnD Insider Compendium HTML blocks.
- exports to xml.
- Powers only so far! Working on other compendium entities. A weekend(s) project...

How to use it:
- requires a html download of the full dndinsider compendium.
- I got a download using: https://github.com/jfpowell/Dungeons-and-Dragons-Insider-Compendium-Downloader
- ^ requires dndinsider account.
- place dnd_compendium_html contents in root folder
- running index.php will cause all powers to be exported as xml files to dnd_compendium_xml/Power/[filename].xml
- check path settings in index.php, and make sure directories exist.
- running this code may take a bit of time.

The Master Plan (where I am heading with this):
- import the entire compendium into a Drupal site, and go nutz.
- create display_suite dispalys and drop compendium entities into printable card templates.
- use nodequeue to allow players to make power lists etc.
