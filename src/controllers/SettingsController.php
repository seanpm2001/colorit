<?php

namespace presseddigital\colorit\controllers;

use craft\web\Controller;

use presseddigital\colorit\Colorit;

class SettingsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex()
    {
        return $this->renderTemplate('colorit/settings', [
            'settings' => Colorit::$settings,
        ]);
    }
}
