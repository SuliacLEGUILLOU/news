<?php

/**
* ownCloud - News
*
* @author Alessandro Cosentino
* @author Bernhard Posselt
* @copyright 2012 Alessandro Cosentino cosenal@gmail.com
* @copyright 2012 Bernhard Posselt nukeawhale@gmail.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\News\Utility;

use \OCA\News\Db\Item;

require_once(__DIR__ . "/../../classloader.php");


class ImportParserTest extends \OCA\AppFramework\Utility\TestUtility {

	private $parser;
	private $time;
	private $in;

	protected function setUp(){
		$this->time = 222;
		$timeFactory = $this->getMockBuilder(
			'\OCA\AppFramework\Utility\TimeFactory')
			->disableOriginalConstructor()
			->getMock();
		$timeFactory->expects($this->any())
			->method('getTime')
			->will($this->returnValue($this->time));

		$this->parser = new ImportParser($timeFactory);
		$this->in = array(
			'items' => array(
				array(
					'id' => "tag:google.com,2005:reader/item/f9fd1dd3c19262e1",
					'title' => "[HorribleSubs] Mushibugyo - 01 [720p].mkv",
    				"published" => 1365415485,
   
				    "alternate" => array( array(
				      "href" => "http://www.nyaa.eu/?page=view&tid=421561",
				      "type" => "text/html"
				    )),
    				"summary" => array(
      					"content" => "1596 seeder(s), 775 leecher(s), 8005 download(s) - 316.7 MiB - Trusted"
      				)
				)
			)
		);
	}


	public function testImportParserReturnsEmptyArrayIfNoInput(){
		$result = $this->parser->parse(array());

		$this->assertEquals($result, array());
	}


	public function testParsesItems() {
		$result = $this->parser->parse($this->in);

		$out = new Item();
		$out->setTitle($this->in['items'][0]['title']);
		$out->setPubDate($this->in['items'][0]['published']);
		$out->setBody($this->in['items'][0]['summary']['content']);
		$out->setUrl($this->in['items'][0]['alternate'][0]['href']);
		$out->setGuid($this->in['items'][0]['id']);
		$out->setGuidHash(md5($this->in['items'][0]['id']));
		$out->setStatus(0);
		$out->setUnread();
		$out->setStarred();

		$this->assertEquals(array($out), $result);
	}


	public function testParsesItemsNoSummary() {
		$this->in['items'][0]['content']['content'] = 'hi';
		unset($this->in['items'][0]['summary']);
		$result = $this->parser->parse($this->in);

		$out = new Item();
		$out->setTitle($this->in['items'][0]['title']);
		$out->setPubDate($this->in['items'][0]['published']);
		$out->setBody($this->in['items'][0]['content']['content']);
		$out->setUrl($this->in['items'][0]['alternate'][0]['href']);
		$out->setGuid($this->in['items'][0]['id']);
		$out->setGuidHash(md5($this->in['items'][0]['id']));
		$out->setStatus(0);
		$out->setUnread();
		$out->setStarred();

		$this->assertEquals(array($out), $result);
	}

}
