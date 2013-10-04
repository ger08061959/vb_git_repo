This is an example page for developers to test some features.

Code 
----

### PHP ###

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed hendrerit ipsum a malesuada cursus. Suspendisse tincidunt elit et aliquet pellentesque. Aenean consequat massa magna, in sodales tortor imperdiet eu. Sed mattis luctus ornare. Vestibulum nec laoreet tortor, tincidunt pharetra nisi. Curabitur nisl mauris, egestas eget nisl ac, vehicula convallis dolor. Nunc egestas consectetur tortor et semper. Fusce ultrices justo aliquet, gravida felis ut, pellentesque lorem. Cras non laoreet turpis, vitae iaculis turpis. Duis placerat sem orci. Etiam fermentum feugiat tempus.

	require_once 'Zend/Uri/Http.php';

	abstract class URI extends BaseURI
	{

	  /**
	   * Returns a URI
	   *
	   * @return  URI
	   */
	  static public function _factory($stats = array(), $uri = 'http')
	  {
	      $uri = explode(':', $uri, 0b10);
	      $schemeSpecific = isset($uri[1]) ? $uri[1] : '';
	      $desc = 'Multi
	line description';

	      // Security check
	      if (!ctype_alnum($scheme)) {
	          throw new Zend_Uri_Exception('Illegal scheme');
	      }

	      return [
	        'uri' => $uri,
	        'value' => null,
	      ];
	  }
	}

### HTML ###

Aliquam erat volutpat. Fusce turpis odio, rhoncus in aliquet eu, fringilla sit amet purus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Duis suscipit ullamcorper ullamcorper. Proin nunc eros, consequat quis eleifend ut, condimentum ac leo. Morbi eget nisl tincidunt, volutpat ipsum at, porta odio. Mauris pulvinar mauris ut elit rutrum aliquet. Duis at eros ante. Sed venenatis blandit neque, id ultrices odio pellentesque at. Sed sollicitudin accumsan orci et porttitor. Donec sit amet dui vel leo pretium tincidunt ac in tellus. Curabitur vel laoreet justo.

	<!DOCTYPE html>
	<title>Title</title>

	<style>body {width: 500px;}</style>

	<script type="application/javascript">
	  function $init() {return true;}
	</script>

	<body>
	  <p checked class="title" id='title'>Title</p>
	  <!-- here goes the rest of the page -->
	</body>

### CSS ###

Praesent erat dolor, tempor ac ornare et, feugiat sagittis velit. Vivamus commodo fringilla urna, et ultrices neque pharetra quis. Nullam dui justo, tincidunt vitae odio pulvinar, euismod vulputate elit. Donec urna eros, dignissim id tincidunt sit amet, sollicitudin sit amet dolor. Aenean iaculis justo eu mauris feugiat dignissim. Etiam sapien ipsum, vestibulum id tellus sit amet, eleifend pulvinar tellus. Aenean bibendum quis neque et sodales. Cras lobortis sapien neque, tincidunt vehicula urna dictum auctor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla metus nisl, auctor quis convallis in, pulvinar sed tellus. Phasellus consequat ultricies ipsum, et pretium purus vestibulum in. Sed luctus ligula condimentum felis tincidunt, sed iaculis dolor gravida. Nam feugiat odio at lacus rhoncus, a blandit massa blandit. Donec ultricies odio eget ligula dignissim, non tincidunt elit eleifend. Maecenas consequat leo sit amet lorem molestie porta.

	@media screen and (-webkit-min-device-pixel-ratio: 0) {
	  body:first-of-type pre::after {
	    content: 'highlight: ' attr(class);
	  }
	  body {
	    background: linear-gradient(45deg, blue, red);
	  }
	}