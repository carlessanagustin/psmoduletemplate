/*
*  @author Carles San Agustin <hello@carlessanagustin.com>
*  @copyright  2013 carlessanagustin.com
*  @license    http://opensource.org/licenses/MIT - MIT License
*/

CREATE TABLE IF NOT EXISTS `_DB_PREFIX_psmoduletemplate` (
  `id_product_start` int(10) unsigned NOT NULL,
  `id_product_end` int(10) unsigned NOT NULL,
  UNIQUE KEY (`id_product_start`,`id_product_end`),
  KEY (`id_product_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

