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

use \OCA\AppFramework\Utility\TimeFactory;

use \OCA\News\Db\Item;


class ImportParser {

	private $timeFactory;

	public function __construct(TimeFactory $timeFactory) {
		$this->timeFactor = $timeFactory;
	}

	public function parse($json){
		$items = array();

		if(array_key_exists('items', $json)) {
			foreach($json['items'] as $entry) {
				$item = new Item();
				$id = $entry['id'];
				$item->setGuid($id);
				$item->setGuidHash(md5($id));
				$item->setTitle($entry['title']);
				$item->setPubDate($entry['published']);
				if(array_key_exists('summary', $entry)) {
					$item->setBody($entry['summary']['content']);
				} elseif(array_key_exists('content', $entry)) {
					$item->setBody($entry['content']['content']);
				}
				
				$item->setUrl($entry['alternate'][0]['href']);
				$item->setStatus(0);
				$item->setStarred();
				$item->setUnread();

				array_push($items, $item);
			}
		}

		return $items;
	}

}
