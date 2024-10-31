<?php

declare(strict_types=1);

namespace PWP\includes\utilities;

use wpdb;

/**
 * wrapper/decorator class for the standard wpdb global object. can be used to retrieve the wpdb global statically, or
 * direclty call some of its more common functionality. the main benefit of this class is that it is typed,
 * whereas the global wpdb value is not.
 */
class PWP_WPDB
{
    private wpdb $db;

    public function __construct()
    {
        $this->db = self::get_wpdb();
    }

    public function prefix(): string
    {
        return $this->db->prefix;
    }

    public function prepare(string $query, ...$args): ?string
    {
        return $this->db->prepare($query, $args);
    }

    public static function get_wpdb(): wpdb
    {
        global $wpdb;
        return $wpdb;
    }

    /**
     * see the wpdb class query function for more about how this function works
     * @param string $query
     * @return int|bool
     */
    public function query(string $query)
    {
        return $this->db->query($query);
    }

    /**
     * see the wpdb class get_results function for more about how this function works
     *
     * @param string $query
     * @return array|object
     */
    public function get_results(string $query)
    {
        return $this->db->get_results($query, OBJECT);
    }

    /**
     * prepare query for term translation updating. works with WPML's wp_icl_translations table.
     *
     * @param string $myLang ISO 639-1 language code of the current entry.
     * @param string $sourceLang ISO 6391 language code of the default/parent entry.
     * @param integer $trid (trid) translations share their trid in the table
     * @param string $elementType identifier for the type of element which is translated. not the same as a taxonomy
     * @param integer $id id of the property which is being translated (taxonomy id, post id, product id, etc.)
     * @return string the completed query as a string
     */
    final public function prepare_term_translation_query(string $myLang, ?string $sourceLang, int $trid, string $elementType, int $id): string
    {
        $table = $this->db->prefix . 'icl_translations';
        $sourceLang = is_null($sourceLang) ? 'NULL' : "{$sourceLang}";

        return $this->filter_proper_NULL($this->db->prepare(
            "UPDATE {$table} SET language_code = %s, source_language_code = %s, trid = %d WHERE element_type = %s AND element_id = %d;",
            $myLang,
            $sourceLang,
            $trid,
            $elementType,
            $id
        ));
    }

    final public function prepare_term_children_query(int $id, string $taxonomy): string
    {
        $table = $this->db->prefix . 'term_taxonomy';

        return $this->filter_proper_NULL($this->db->prepare(
            "SELECT term_id FROM {$table} where parent = %d AND taxonomy = %s;",
            $id,
            $taxonomy,
        ));
    }

    /**
     * will replace 'NULL' in a query with NULL.
     * 
     * This function is to implement proper NULL into wpdb-prepare() functions, which otherwise will not do it accurately.
     *
     * @param string $query
     * @return string
     */
    private function filter_proper_NULL(string $query): string
    {
        return str_replace("'NULL'", "NULL", $query);
    }
}
