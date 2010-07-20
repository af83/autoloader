<?php

/**
 * Copyright (c) 2009-2010, AF83
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the University of California, Berkeley nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHORS AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHORS AND CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This tools help you to build an efficient (I guess...) class map for your app.
 * It provide helpers to
 *   - check a path for some extension (default is php) and parse for php class.
 *   - cache it.
 *   - only load cache
 *   - register in spl autoloader queue
 *   - configure some path checking using a ".autoloader.php" file
 * @credits Inspired from http://anthonybush.com/projects/autoloader/source/
 * @fixme log cache file change, and collision. (maybe in /tmp/autoloader.log).
 * @todo phing target 'autoloader'.
 * @todo simpleTest, Oh yes, test me, I know you love to make test :)
 * @package Toupti
 */
class Autoloader
{

  protected static $cached_class_map = array();

  private $cache_file = null;

  /** 
   *
   */
  function __construct($cache_file)
  {
    $this->cache_file = $cache_file;
    $this->load_cache();
  }

  /**
   * @return array($class_name => $file_path) the dir class map
   */
  function parse_dir($dir_name)
  {
    // if $dir_name is a dir
    // then look for '.autoloader.php', if it exists, load and check it using $this->use_file_config()
    // else 
    //     grep each .php, for "/^ *class *(\/w)", then append filename/classname to the cache map
    //      recurse ourself on each dir.
    // return results \o/
  }

  function add_entry($classname, $file_path)
  {
    self::$cached_class_map[$classname] = $file_path;
  }

  function save_cache()
  {
    // serialize and save.
    if (!is_null($this->cache_file) && (!file_exists($this->cache_file) || $this->cache_file)) {
      $fileContents = serialize(self::$cached_class_map);
      $bytes = file_put_contents($this->cache_file, $fileContents);
      if ($bytes === false) {
        throw new AutoloaderException('Autoloader could not write the cache file: ' . $this->cache_file);
      }
    } else {
      throw new AutoloaderException('Autoload cache file not writable: ' . $this->cache_file);
    }
  }

  function load_cache()
  {
      if (!is_null($this->cache_file) && is_file($this->cache_file)) 
      {
          self::$cached_class_map = unserialize(file_get_contents($this->cache_file));
      }
  }

  function register()
  {
    // load the cache file
    spl_autoload_register(array('Autoloader', 'load_class'));
  }

  public static function load_class($classname)
  {
    if( array_key_exists($classname, self::$cached_class_map) )
    {
      return require_once(self::$cached_class_map[$classname]) ;
    }
    return false;
  }

}

class AutoloaderException extends Exception {}
