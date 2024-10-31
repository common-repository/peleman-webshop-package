<?php

declare(strict_types=1);

namespace PWP\includes\services\entities;

use DateTime;
use JsonSerializable;
use PWP\includes\wrappers\PDF_Upload;
use Serializable;
use stdClass;
use wpdb;

use function PHPUnit\Framework\isNull;

class Project implements I_Entity, JsonSerializable
{
    private int $id;
    private int $user_id;
    private string $project_id;
    private int $product_id;
    private string $path;
    private string $file_name;
    private int $pages;
    private float $price_vat_excl;
    private string $created;
    private string $updated;
    private string $ordered;

    /**
     * Create PDF project for prdocut
     *
     * @param integer $userId id of the user. if user is not logged in, should be 0
     * @param integer $productId id of the product this pdf is to be applied to
     * @param string $fileName original name of the file, given by the uploader/user
     * @param integer $pages amount of pages in the PDF
     * @param float $price_vat_excl total price of the PDF, calculated from the amount of pages and cost per page of the product
     */
    private function __construct(int $userId, int $productId, string $fileName, int $pages = 0, float $price_vat_excl = 0.0)
    {
        $this->id = -1;
        $this->user_id = $userId;
        $this->project_id = '';
        $this->product_id = $productId;
        $this->file_name = $fileName;
        $this->pages = $pages;
        $this->price_vat_excl = $price_vat_excl;
        $this->created = '';
        $this->updated = '';
        $this->ordered = '';
    }

    final public static function get_by_id(int $id): ?self
    {
        global $wpdb;
        $table_name = $wpdb->prefix . PWP_PROJECTS_TABLE;

        $sql = "SELECT * from {$table_name} where id = %d";
        $row = $wpdb->get_row($wpdb->prepare($sql, $id));

        if (!$row) return null;
        return self::create_new_from_row($row);
    }

    private static function create_new_from_row(object $row): self
    {
        $product = new self(
            (int)$row->user_id,
            (int)$row->product_id,
            $row->file_name,
            (int)$row->pages,
            (float)$row->price_vat_excl,
        );
        $product->id            = (int)$row->id;
        $product->project_id    = $row->project_id ?? '';
        $product->created       = $row->created;
        $product->updated       = $row->updated;
        $product->ordered       = $row->ordered;
        $product->path          = $row->path;
        return $product;
    }

    public static function create_new(int $userId, int $productId, string $fileName, int $pages = 0, float $price = 0.00): self
    {
        return new self($userId, $productId, $fileName, $pages, $price);
    }

    #region setters
    public function set_ordered(): void
    {
        $this->ordered = wp_date('Y-m-d H:i:s', time());
    }

    public function set_user_id(int $id): void
    {
        $this->id = $id;
    }

    public function set_project_id(string $id): void
    {
        $this->project_id = $id;
    }

    public function set_product_id(int $id): void
    {
        $this->product_id = $id;
    }

    /**
     * Set file name of the project/pdf file.
     *
     * @param string $file
     * @return void
     */
    public function set_file_name(string $file): void
    {
        $this->file_name = $file;
    }

    /**
     * Set amount of pages in the project. Two pages constitute a sheet
     *
     * @param integer $pages
     * @return void
     */
    public function set_pages(int $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * Set project price without VAT 
     *
     * @param float $price
     * @return void
     */
    public function set_price(float $price): void
    {
        $this->price_vat_excl = $price;
    }

    #endregion
    #region getters
    public function get_id(): int
    {
        return $this->id;
    }

    public function get_created(): DateTime
    {
        return new DateTime($this->created);
    }

    public function get_updated(): DateTime
    {
        return new DateTime($this->updated);
    }

    public function get_ordered(): ?DateTime
    {
        return $this->ordered ? new DateTime($this->ordered) : null;
    }

    public function was_ordered(): bool
    {
        return !empty($this->ordered) && $this->ordered !== '0000-00-00 00:00:00';
    }

    public function get_path(bool $relative = false): string
    {
        $path = $this->path;
        if ($relative) $path = PWP_UPLOAD_DIR . $path;
        return $path;
    }

    public function get_file_name(): string
    {
        return $this->file_name;
    }

    public function get_user_id(): int
    {
        return $this->user_id;
    }

    public function get_pages(): int
    {
        return $this->pages;
    }

    public function get_price_vat_excl(): float
    {
        return $this->price_vat_excl;
    }

    public function get_project_id(): string
    {
        return $this->project_id ?: null;
    }

    public function get_product_id(): int
    {
        return $this->product_id;
    }
    #endregion

    public function persist(): void
    {
        if (-1 !== $this->id) {
            //if id is more than 0, this project already exists in the database
            $this->update();
            return;
        }
        $this->save();
    }

    public function delete(): void
    {
        if (-1 === $this->id) return;

        global $wpdb;
        if ($wpdb instanceof \wpdb) {
            if (!$wpdb->delete(
                $wpdb->prefix . PWP_PROJECTS_TABLE,
                array('id' => $this->id),
                array('%d')
            ));
        }
    }

    public function save_file(PDF_Upload $pdf): void
    {
        $safeName = uniqid();
        $this->path = "{$safeName}.pdf";
        if (!move_uploaded_file($pdf->get_tmp_name(), $this->get_path(true))) {
            throw new \Exception("something went wrong trying to save the uploaded .pdf file.", 500);
        }

        $this->save();
    }

    private function save(): void
    {
        global $wpdb;
        if ($wpdb instanceof \wpdb) {
            $result = $wpdb->insert(
                $wpdb->prefix . PWP_PROJECTS_TABLE,
                $this->db_data_array(),
                $this->db_data_format_array(),
            );
            if (!$result) {
                throw new \Exception("Encountered problem when trying to insert project into database. Check logs for more information");
            }
        }
        $this->id = $wpdb->insert_id;
    }

    private function update(): void
    {
        global $wpdb;
        // if ($wpdb instanceof \wpdb) {
        $result = $wpdb->update(
            $wpdb->prefix . PWP_PROJECTS_TABLE,
            $this->db_data_array(),
            array('id' => $this->id),
            $this->db_data_format_array(),
            array('%d'),
        );
        if (!$result) {
            throw new \Exception("Encountered problem when trying to update project in database. Check logs for more information.");
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->data();
    }

    public function delete_files(): void
    {
        $path = $this->get_path(true);
        array_map('unlink', array_filter((array) glob($path) ?: []));
        // rmdir($path);
        // unlink($path,);
    }

    private function db_data_array(): array
    {
        return array(
            'user_id'           => $this->user_id,
            'project_id'        => $this->project_id,
            'product_id'        => $this->product_id,
            'path'              => $this->path,
            'file_name'         => $this->file_name,
            'pages'             => $this->pages,
            'price_vat_excl'    => $this->price_vat_excl,
            'ordered'           => $this->ordered,
        );
    }

    private function db_data_format_array(): array
    {
        return array('%d', '%s', '%d', '%s', '%s', '%d', '%f', '%s');
    }

    public function data()
    {
        return array(
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'project_id'        => $this->project_id,
            'product_id'        => $this->product_id,
            'file_name'         => $this->file_name,
            'pages'             => $this->pages,
            'price_vat_excl'    => $this->price_vat_excl,
            'created'           => $this->created,
            'updated'           => $this->updated,
            'ordered'           => $this->ordered,
        );
    }

    /**
     * search and retrieve all unordered projects in the database
     *
     * @return Project[]
     */
    public static function get_all_unordered_projects(): array
    {
        global $wpdb;

        $table = $wpdb->prefix . PWP_PROJECTS_TABLE;
        $sql = "SELECT * FROM {$table} 
        WHERE ordered = '0000-0-0 00:00-00' 
        OR ordered IS NULL";

        $results = $wpdb->get_results($sql, OBJECT_K);

        $projects = array();
        foreach ($results as $id => $project) {
            $projects[$id] = self::create_new_from_row($project);
        }

        return $projects;
    }
}
