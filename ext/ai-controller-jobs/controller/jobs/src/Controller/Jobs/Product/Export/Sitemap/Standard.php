<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Product\Export\Sitemap;


/**
 * Job controller for product sitemap.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Standard
	extends \Aimeos\Controller\Jobs\Product\Export\Standard
	implements \Aimeos\Controller\Jobs\Iface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Product site map' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Creates a product site map for search engines' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$container = $this->createContainer();

		$files = $this->export( $container );
		$this->createSitemapIndex( $container, $files );

		$container->close();
	}


	/**
	 * Adds the given products to the content object for the site map file
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $content File content object
	 * @param \Aimeos\MShop\Product\Item\Iface[] $items List of product items
	 */
	protected function addItems( \Aimeos\MW\Container\Content\Iface $content, array $items )
	{
		$config = $this->getContext()->getConfig();

		/** controller/jobs/product/export/sitemap/changefreq
		 * Change frequency of the products
		 *
		 * Depending on how often the product content changes (e.g. price updates)
		 * and the site map files are generated you can give search engines a
		 * hint how often they should reindex your site. The site map schema
		 * allows a few pre-defined strings for the change frequency:
		 * * always
		 * * hourly
		 * * daily
		 * * weekly
		 * * monthly
		 * * yearly
		 * * never
		 *
		 * More information can be found at
		 * {@link http://www.sitemaps.org/protocol.html#xmlTagDefinitions sitemap.org}
		 *
		 * @param string One of the pre-defined strings (see description)
		 * @since 2015.01
		 * @category User
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/container/options
		 * @see controller/jobs/product/export/sitemap/location
		 * @see controller/jobs/product/export/sitemap/max-items
		 * @see controller/jobs/product/export/sitemap/max-query
		 */
		$changefreq = $config->get( 'controller/jobs/product/export/sitemap/changefreq', 'daily' );

		/** controller/jobs/product/export/sitemap/standard/template-items
		 * Relative path to the XML items template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map files. The configuration string is the path
		 * to the template file relative to the templates directory (usually in
		 * controller/jobs/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating XML code for the site map items
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/standard/template-header
		 * @see controller/jobs/product/export/sitemap/standard/template-footer
		 * @see controller/jobs/product/export/sitemap/standard/template-index
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/standard/template-items';
		$default = 'product/export/sitemap-items-body-default.xml';

		$context = $this->getContext();
		$view = $context->getView();

		$view->siteItems = $items;
		$view->siteFreq = $changefreq;

		$content->add( $view->render( $context->getConfig()->get( $tplconf, $default ) ) );
	}


	/**
	 * Creates a new container for the site map file
	 *
	 * @return \Aimeos\MW\Container\Iface Container object
	 */
	protected function createContainer()
	{
		$config = $this->getContext()->getConfig();

		/** controller/jobs/product/export/sitemap/location
		 * Directory where the generated site maps should be placed into
		 *
		 * The site maps must be publically available for download by the search
		 * engines. Therefore, you have to configure a directory for the site
		 * maps in your web space that is writeable by the process generating
		 * the files, e.g.
		 *
		 * /var/www/yourshop/your/sitemap/path
		 *
		 * The location of the site map index file should then be
		 * added to the robots.txt in the document root of your domain:
		 *
		 * Sitemap: https://www.yourshop.com/your/sitemap/path/aimeos-sitemap-index.xml
		 *
		 * The "sitemapindex-aimeos.xml" file is the site map index file that
		 * references the real site map files which contains the links to the
		 * products. Please make sure that the protocol and domain
		 * (https://www.yourshop.com/) is the same as the ones used in the
		 * product links!
		 *
		 * More details about site maps can be found at
		 * {@link http://www.sitemaps.org/protocol.html sitemaps.org}
		 *
		 * @param string Absolute directory to store the site maps into
		 * @since 2015.01
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/export/sitemap/container/options
		 * @see controller/jobs/product/export/sitemap/max-items
		 * @see controller/jobs/product/export/sitemap/max-query
		 * @see controller/jobs/product/export/sitemap/changefreq
		 */
		$location = $config->get( 'controller/jobs/product/export/sitemap/location', sys_get_temp_dir() );

		/** controller/jobs/product/export/sitemap/container/options
		 * List of file container options for the site map files
		 *
		 * The directory and the generated site map files are stored using
		 * container/content objects from the core, namely the "Directory"
		 * container and the "Binary" content classes. Both implementations
		 * support some options:
		 * * dir-perm (default: 0755): Permissions if the directory must be created
		 * * gzip-level (default: 5): GZip compression level from 0 to 9 (0 = fast, 9 = best)
		 * * gzip-mode (default: "wb"): Overwrite existing files in binary mode
		 *
		 * @param array Associative list of option name/value pairs
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/location
		 * @see controller/jobs/product/export/sitemap/max-items
		 * @see controller/jobs/product/export/sitemap/max-query
		 * @see controller/jobs/product/export/sitemap/changefreq
		 */
		$default = array( 'gzip-mode' => 'wb' );
		$options = $config->get( 'controller/jobs/product/export/sitemap/container/options', $default );

		return \Aimeos\MW\Container\Factory::getContainer( $location, 'Directory', 'Gzip', $options );
	}


	/**
	 * Creates a new site map content object
	 *
	 * @param \Aimeos\MW\Container\Iface $container Container object
	 * @param integer $filenum New file number
	 * @return \Aimeos\MW\Container\Content\Iface New content object
	 */
	protected function createContent( \Aimeos\MW\Container\Iface $container, $filenum )
	{
		/** controller/jobs/product/export/sitemap/standard/template-header
		 * Relative path to the XML site map header template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map header. The configuration string is the path
		 * to the template file relative to the templates directory (usually in
		 * controller/jobs/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating XML code for the site map header
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/standard/template-items
		 * @see controller/jobs/product/export/sitemap/standard/template-footer
		 * @see controller/jobs/product/export/sitemap/standard/template-index
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/standard/template-header';
		$default = 'product/export/sitemap-items-header-default.xml';

		$context = $this->getContext();
		$view = $context->getView();

		$content = $container->create( $this->getFilename( $filenum ) );
		$content->add( $view->render( $context->getConfig()->get( $tplconf, $default ) ) );
		$container->add( $content );

		return $content;
	}


	/**
	 * Closes the site map content object
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $content
	 */
	protected function closeContent( \Aimeos\MW\Container\Content\Iface $content )
	{
		/** controller/jobs/product/export/sitemap/standard/template-footer
		 * Relative path to the XML site map footer template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map footer. The configuration string is the path
		 * to the template file relative to the templates directory (usually in
		 * controller/jobs/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating XML code for the site map footer
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/standard/template-header
		 * @see controller/jobs/product/export/sitemap/standard/template-items
		 * @see controller/jobs/product/export/sitemap/standard/template-index
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/standard/template-footer';
		$default = 'product/export/sitemap-items-footer-default.xml';

		$context = $this->getContext();
		$view = $context->getView();

		$content->add( $view->render( $context->getConfig()->get( $tplconf, $default ) ) );
	}


	/**
	 * Adds the content for the site map index file
	 *
	 * @param \Aimeos\MW\Container\Iface $container File container object
	 * @param array $files List of generated site map file names
	 */
	protected function createSitemapIndex( \Aimeos\MW\Container\Iface $container, array $files )
	{
		/** controller/jobs/product/export/sitemap/standard/template-index
		 * Relative path to the XML site map index template of the product site map job controller.
		 *
		 * The template file contains the XML code and processing instructions
		 * to generate the site map index files. The configuration string is the path
		 * to the template file relative to the templates directory (usually in
		 * controller/jobs/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating XML code for the site map index
		 * @since 2015.01
		 * @category Developer
		 * @see controller/jobs/product/export/sitemap/standard/template-header
		 * @see controller/jobs/product/export/sitemap/standard/template-items
		 * @see controller/jobs/product/export/sitemap/standard/template-footer
		 */
		$tplconf = 'controller/jobs/product/export/sitemap/standard/template-index';
		$default = 'product/export/sitemap-index-default.xml';

		$context = $this->getContext();
		$view = $context->getView();

		$view->siteFiles = $files;

		$content = $container->create( 'aimeos-sitemap-index.xml' );
		$content->add( $view->render( $context->getConfig()->get( $tplconf, $default ) ) );
		$container->add( $content );
	}


	/**
	 * Returns the configuration value for the given name
	 *
	 * @param string $name One of "domain", "max-items" or "max-query"
	 * @param mixed $default Default value if name is unknown
	 * @return mixed Configuration value
	 */
	protected function getConfig( $name, $default = null )
	{
		$config = $this->getContext()->getConfig();

		switch( $name )
		{
			case 'domain':
				return [];

			case 'max-items':
				/** controller/jobs/product/export/sitemap/max-items
				 * Maximum number of products per site map
				 *
				 * Each site map file must not contain more than 50,000 links and it's
				 * size must be less than 10MB. If your product URLs are rather long
				 * and one of your site map files is bigger than 10MB, you should set
				 * the number of products per file to a smaller value until each file
				 * is less than 10MB.
				 *
				 * More details about site maps can be found at
				 * {@link http://www.sitemaps.org/protocol.html sitemaps.org}
				 *
				 * @param integer Number of products per file
				 * @since 2015.01
				 * @category Developer
				 * @category User
				 * @see controller/jobs/product/export/sitemap/container/options
				 * @see controller/jobs/product/export/sitemap/location
				 * @see controller/jobs/product/export/sitemap/max-query
				 * @see controller/jobs/product/export/sitemap/changefreq
				 */
				return $config->get( 'controller/jobs/product/export/sitemap/max-items', 50000 );

			case 'max-query':
				/** controller/jobs/product/export/sitemap/max-query
				 * Maximum number of products per query
				 *
				 * The products are fetched from the database in bunches for efficient
				 * retrieval. The higher the value, the lower the total time the database
				 * is busy finding the records. Higher values also means that record
				 * updates in the tables need to wait longer and the memory consumption
				 * of the PHP process is higher.
				 *
				 * @param integer Number of products per query
				 * @since 2015.01
				 * @category Developer
				 * @see controller/jobs/product/export/sitemap/container/options
				 * @see controller/jobs/product/export/sitemap/location
				 * @see controller/jobs/product/export/sitemap/max-items
				 * @see controller/jobs/product/export/sitemap/changefreq
				 */
				return $config->get( 'controller/jobs/product/export/sitemap/max-query', 1000 );
		}

		return $default;
	}


	/**
	 * Returns the file name for the new content file
	 *
	 * @param integer $number Current file number
	 * @return string New file name
	 */
	protected function getFilename( $number )
	{
		return sprintf( 'aimeos-sitemap-%d.xml', $number );
	}
}
