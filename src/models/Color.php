<?php

namespace presseddigital\colorit\models;

use Craft;

use craft\base\Model;
use craft\validators\ColorValidator as ColorValidator;
use presseddigital\colorit\helpers\ColorHelper;

class Color extends Model
{
    // Constants
    // =========================================================================

    const TRANSPARENT_STRING = 'transparent';

    // Private Properties
    // =========================================================================

    private $_palette;
    private $_hex;

    // Public Properties
    // =========================================================================

    public function init()
    {
        parent::init();
    }

    public $handle;
    public $custom;
    public $opacity = 100;
    public $field;


    // Public Methods
    // =========================================================================

    public function __toString(): string
    {
        return $this->getColor();
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['handle', 'string'];
        $rules[] = ['custom', ColorValidator::class, 'when' => [$this, 'isCustomColor'], 'message' => Craft::t('colorit', 'A valid custom color hex is required')];
        $rules[] = ['opacity', 'required'];
        $rules[] = ['opacity', 'default', 'value' => 100];
        $rules[] = ['opacity', 'integer', 'min' => 0, 'max' => 100];
        return $rules;
    }

    public function getPalette()
    {
        if (is_null($this->_palette)) {
            $this->_palette = $this->field->getPalette() ?? [];
        }
        return $this->_palette;
    }

    public function isCustomColor(): bool
    {
        return $this->handle == '_custom_';
    }

    public function isTransparent(): bool
    {
        return $this->handle == self::TRANSPARENT_STRING;
    }

    public function getColor(string $format = null)
    {
        $color = '';
        if ($this->isTransparent()) {
            return self::TRANSPARENT_STRING;
        }

        $format = $format ?? $this->field->colorFormat;
        switch ($format) {
            case 'hex':
                $color = $this->getHex();
                break;
            case 'hashhex':
                $color = $this->getHashHex();
                break;
            case 'rgb':
                $color = $this->getRgb();
                break;
            case 'rgba':
                $color = $this->getRgba();
                break;
            default:
                $color = $this->opacity < 100 ? $this->getRgba() : ($this->isCustomColor() ? $this->getHex() : $this->getHashHex());
                break;
        }
        return $color ? $color : '';
    }

    public function hasColor()
    {
        return !empty($this->getColor());
    }

    public function getHex()
    {
        if ($this->isTransparent()) {
            return self::TRANSPARENT_STRING;
        }

        if (is_null($this->_hex)) {
            $hex = '';
            if ($this->handle) {
                switch ($this->handle) {
                    case '_custom_':
                        $hex = $this->custom;
                        break;

                    default:
                        $hex = $this->_inPalette($this->handle) ? $this->_palette[$this->handle]['color'] : '';
                        break;
                }
            }

            if (empty($hex) || !ColorHelper::isValidHex($hex)) {
                return '';
            }

            $this->_hex = $hex;
        }
        return $this->_hex;
    }

    public function getHashHex()
    {
        if ($this->isTransparent()) {
            return self::TRANSPARENT_STRING;
        }

        $hex = $this->getHex();
        if ($hex) {
            return '#' . $hex;
        }
        return false;
    }

    public function getRgb()
    {
        if ($this->isTransparent()) {
            return self::TRANSPARENT_STRING;
        }

        $hex = $this->getHex();
        if ($hex) {
            $rgb = ColorHelper::hexToRgb($hex);
            if ($rgb) {
                return $rgb;
            }
        }
        return false;
    }

    public function getRgba()
    {
        if ($this->isTransparent()) {
            return self::TRANSPARENT_STRING;
        }

        $hex = $this->getHex();
        if ($hex) {
            $rgba = ColorHelper::hexToRgba($hex, $this->opacity);
            if ($rgba) {
                return $rgba;
            }
        }
        return false;
    }

    public function getR()
    {
        $hex = $this->getHex();
        if ($hex) {
            $rgb = ColorHelper::hexToRgb($hex, true);
            if ($rgb) {
                return $rgb['r'];
            }
        }
        return false;
    }

    public function getRed()
    {
        return $this->getR();
    }

    public function getG()
    {
        $hex = $this->getHex();
        if ($hex) {
            $rgb = ColorHelper::hexToRgb($hex, true);
            if ($rgb) {
                return $rgb['g'];
            }
        }
        return false;
    }

    public function getGreen()
    {
        return $this->getG();
    }

    public function getB()
    {
        $hex = $this->getHex();
        if ($hex) {
            $rgb = ColorHelper::hexToRgb($hex, true);
            if ($rgb) {
                return $rgb['b'];
            }
        }
        return false;
    }

    public function getBlue()
    {
        return $this->getB();
    }

    public function getA()
    {
        if ($this->isTransparent()) {
            return 0;
        }

        return $this->opacity / 100;
    }

    public function getAlpha()
    {
        return $this->getA();
    }

    // UK Versions
    // =========================================================================

    public function getColour(string $format = null)
    {
        return $this->getColor($format);
    }

    public function hasColour(string $format = null)
    {
        return $this->hasColor($format);
    }

    public function isCustomColour(): bool
    {
        return $this->isCustomColor();
    }

    // Private Methods
    // =========================================================================


    private function _inPalette(string $handle)
    {
        return array_key_exists($handle, $this->getPalette());
    }
}
