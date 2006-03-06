<?php

/*
 * This file is part of UyO.no.
 * 
 * UyO.no is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * UyO.no is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with UyO.no; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * A group (most likely the uni course) entries can belong to
 */
class Group {
	var $code;
	var $name;
	var $desc;
	var $bordercolor;
	var $bgcolor;

	/**
	 * Group constructor
	 *
	 * @param string the code of this Group ("fagkode")
	 * @param string the name of this Group (e.g. 'Fysikk - Mekanikk')
	 * @param string HTMLized RGB color of the border
	 * @param string HTMLized RGB color of the background
	 */
		function Group($code, $name, $bc = '#0a8', $bgc = '#0f0') {// Do I have to specify default values here, or above?
	$this->code = $code;
		$this->name = $name;
		$this->bordercolor = $bc;
		$this->bgcolor = $bgc;
	}
} // end Group

/**
 * An entry on the site
 */
class Entry {
	var $group;
	var $title;
	var $text;
	var $id;
	var $links;
	var $type;
	var $status;

	/**
	 * Entry constructor
	 *
	 * @param Group the Group this Entry belongs to
	 * @param string the meat of this Entry
	 * @param array any links in this Entry
	 */
	function Entry($group, $text, $title, $id, $links, $type, $status = 'automatic', $author = 'auto') {
		$this->group = & $group; // should this be a reference or not?
		$this->text = $text;
		$this->title = $title;
		$this->type = $type;
		$this->status = $status;
		$this->id = $id;
		$this->links = $links;
		$this->author = $author;

		// FIXME: Use TempStore to fetch all comments to this post as array of Comments
		/*
		  // Using foreign keys
		  SELECT c.* FROM entries e, comments c WHERE e.id = '$id';
		*/
	}

	/**
	 * Returns the entry as string (TODO: according to requested format)
	 */
	function toString($format = 'html') {
		switch ($format) {
			case 'html' :
				// Box, then comments
				$result = '
								      <div class="box">
								        <div class="comments">
												  <a href="javascript:openComments('.$this->id.')" title="'; // FIXME: Javascript goodness required

				if ($this->comments.count == 1) {
					$result .= '1 comment';
				} else {
					$result .= $this->comments.count.' comments';
				}

				// Below is the number ($this->comments.count) that will appear on the right side of the entry box
				$result .= ' on this entry.">
												  	'.$this->comments.count.'
											  	</a>
										  	</div>
								        <div class="entry_auto">';

				// Entry type indicator (documents, links, time/date rescheduling, important)
				$result .= '
								          <div class="inline">
								            <a href="'.$this->type->permalink.'" title="'.$this->type->longname.'">
								              <img src="'.$this->type->image.'" alt="'.$this->type->shortname.'" />
								            </a>
								          </div>
								          <div class="info">
								            ';

				// Status indicator (automatic/manual entry + profile link)
				if ($this->status == 'manual') {
					$result .= '<a href="profile.php?id='.$this->author->id.'"><img src="'.$this->author->tinyimageurl.'" alt="Manual entry" title="Manual entry by '.$this->author->name.' at '.date("Y-m-d H:i", $this->modifieddate).'." /></a>';
				} else {
					// FIXME: find out what to do with the tinyautoentryimageurl thingy.
					$result .= '<img src="'.$tinyautoentryimageurl.' alt="Automatic entry" title="Automatic entry at '.date("Y-m-d H:i", $this->modifieddate).'." />';
				}

				$result .= '
								         </div>
								          <h2>'.$this->title.'</h2>
								          <p>
								          '.$this->text.'
								          </p>';

				// Links
				if ($this->links.count > 0) {
					$result .= '
												      <div class="links">';

					foreach ($this->links as $link) {
						$result .= '
																    <a href="'.$link->url.'" title="'.$link->type->longname.' - '.$link->name.'">
																      <img src="'.$link->type->image.'" alt="'.$link->type->shortname.'" />
																      '.$link->name.'
														        </a>';
					}

					if ($this->links.count > 0) {
						$result .= '
														      </div>';
					}

					$result .= '
										        </div>
										      </div>
										';
					break;
				}
				return $result;
		} // end toString()
	} // end Entry
}

// Comment class : is it easier to do this simply aspect-oriented? _should_ it be OO?
/*
class Comment {
	function Comment($id = '', $ip = '', $email = '', $url = '', $date, ) {
	}
}
*/

class Link {
	var $url;
	var $name;
	var $type;

	function Link($url, $name, $type) {
		// FIXME: regex URL verification?
		$this->url = $url;
		$this->name = $name;
		$this->type = $type;
	}
}

class LinkType {
	var $longname;
	var $shortname;
	var $image;

	function LinkType($longname, $shortname, $image) {
		$this->longname = $longname;
		$this->shortname = $shortname;
		// FIXME: regex URL verification
		$this->image = $image;
	}
}

class EntryType {
	var $longname;
	var $shortname;
	var $image;
	var $permalink;

	function EntryType($longname, $shortname, $image, $permalink) {
		$this->longname = $longname;
		$this->shortname = $shortname;
		// FIXME: regex URL verification
		$this->image = $image;
		// FIXME: permalinks for viewing only entries belonging to certain categories
		$this->permalink = $permalink;
	}
}
?>