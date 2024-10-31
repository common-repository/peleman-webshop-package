<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use WC_Product;

/**
 * wrapper class for PIE Editor Instruction metadata on products and product variations, and handling wordpress
 * metadata serialization of arrays.
 * PIE Editor Instructions are stored as a string of instructions separated by spaces.
 */
class PIE_Editor_Instructions extends Product_Meta
{
    public const EDITOR_INSTRUCTIONS_KEY    = 'pie_editor_instructions';
    public const INSTRUCTION_PREFIX         = 'pie_instruct_';

    /**
     * contained array of individual PIE instructions
     *
     * @var PIE_Instruction[]
     */
    private array $instructions;

    public function __construct(WC_Product $parent)
    {
        $this->parent = $parent;
        $this->instructions = array();

        $this->add_instruction('usedesigns', 'use designs', true);
        $this->add_instruction('usebackgrounds', 'use backgrounds', true);
        $this->add_instruction('uselayers', 'use layers', true);

        $this->add_instruction('useimageupload', 'use image upload', true);
        $this->add_instruction('useelements', 'use elements', true);
        $this->add_Instruction('useartwork', 'use artwork', false);

        $this->add_instruction('usestockphotos', 'use stock photos', true);
        $this->add_instruction('useqr', 'use QR code', true);
        $this->add_instruction('useshowsafezone', 'use show safe zone', true);

        $this->add_instruction('usetext', 'use text', true);
        $this->add_instruction('usesettings', 'use settings', true);
        $this->add_instruction('useshowcropzone', 'use show cropzone', true);

        $this->add_Instruction('useautoflow', 'use auto flow', false);
        $this->add_Instruction('usenotes', 'use notes', false);
        $this->add_Instruction('usepagenavigator', 'use page navigator', false);


        // $this->add_instruction('usedownloadpreview', 'use download preview');
        // $this->add_instruction('useopenfile', 'use open file');
        // $this->add_instruction('usedesignmode', 'use design mode');

        $this->parse_instructions_from_meta();
    }

    private function parse_instructions_from_meta()
    {
        $instructionString = $this->parent->get_meta(self::EDITOR_INSTRUCTIONS_KEY);
        if (empty($instructionString)) {
            return;
        }

        $this->set_instructions_from_string($instructionString);
    }

    public function set_instructions_from_string(string $instructionString): void
    {
        $instructionArray = $instructionString ? explode(' ', $instructionString) : [];

        foreach ($this->instructions as $key => $instruction) {
            $instruction->set_enabled(in_array($key, $instructionArray));
        }
    }

    public function add_instruction(string $key, string $label, bool $enabled = false, string $description = ''): self
    {
        $instruction = new PIE_Instruction($key, $label, $enabled, $description);
        $this->instructions[$key] = $instruction;
        return $this;
    }

    public function remove_instruction(string $key): self
    {
        unset($this->instructions[$key]);
        return $this;
    }

    public function parse_instruction_array(array $instructions): self
    {
        foreach ($this->instructions as $key => $instruction) {
            $instruction->set_enabled(isset($instructions[$key]));
        }
        return $this;
    }

    public function parse_instruction_array_loop(array $instructions, int $loop): self
    {
        foreach ($this->instructions as $key => $instruction) {
            $instruction->set_enabled(isset($instructions[$key][$loop]));
        }
        return $this;
    }

    /**
     * returns an array key-value pairs representing editor instructions
     *
     * @return PIE_Instruction[]
     */
    public function get_instructions(): array
    {
        return $this->instructions;
    }

    public function update_meta_data(): void
    {
        $this->parent->update_meta_data(
            self::EDITOR_INSTRUCTIONS_KEY,
            $this->get_instructions_string()
        );
    }

    public function get_instructions_string(): string
    {
        $arr = [];
        foreach ($this->instructions as $key => $instruction) {
            if ($instruction->is_enabled()) {
                $arr[] = $key;
            }
        }
        return implode(' ', $arr);
    }

    public function get_instruction_array(): array
    {
        $arr = [];
        foreach ($this->instructions as $key => $instruction) {
            if ($instruction->is_enabled()) {
                $arr[] = $key;
            }
        }
        return $arr;
    }
}
