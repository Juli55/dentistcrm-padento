<?php

namespace App\Services;

use App\Settings;

class Setting
{
    public $lang = 'de';

    function __construct($lang = 'de')
    {
        $this->lang = $lang;
    }

    /**
     * Get setting
     *
     * @param string $name
     * @return Settings|null
     */
    private function getSetting($name)
    {
        if ($this->lang != 'de') {
            $name_at = $name . ' ' . strtoupper($this->lang);
            if (Settings::where('name', $name_at)->first()) {
              $name = $name_at;
            }
        }

        return Settings::where('name', $name)->first();
    }

    /**
     * Get setting value
     *
     * @param string $name
     * @return string|null
     */
    private function getSettingValue($name)
    {
        $setting = $this->getSetting($name);

        return $setting ? $setting->value : null;
    }

    /**
     * Get welcome video
     *
     * @return string|null
     */
    public function getWelcomeVideo()
    {
        return $this->getSettingValue('introduction vimeo welcome video');
    }

    /**
     * Get form video
     *
     * @return string|null
     */
    public function getFormVideo()
    {
        return $this->getSettingValue('Padento Formular Video');
    }

    /**
     * Get start page heading
     *
     * @return string|null
     */
    public function getStartPageHeading()
    {
        return $this->getSettingValue('Hauptüberschrift');
    }

    /**
     * Get start page paragraph
     *
     * @return string|null
     */
    public function getStartPageParagraph()
    {
        return $this->getSettingValue('Text unterhalb Hauptüberschrift');
    }

    /**
     * Get start page video code
     *
     * @return string|null
     */
    public function getStartPageVideoCode()
    {
        return $this->getSettingValue('Wistia Video Code');
    }

    public function getFindBestDentures()
    {
        return $this->getSettingValue('Textbereich 1');
    }

    public function getSequence()
    {
        return $this->getSettingValue('Textbereich 2');
    }

    public function getErrorEmailAddress()
    {
        return $this->getSettingValue('Error Email Address');
    }

    public function getNoLabFoundTitle()
    {
        return $this->getSettingValue('No Labs Found Title');
    }

    public function getNoLabFoundBody()
    {
        return $this->getSettingValue('No Labs Found Body');
    }

    public function getDevEmailAddress()
    {
        return $this->getSettingValue('Dev Email Address');
    }

    public function getFormData()
    {
        return [
            'heading'         => $this->getSettingValue('Padento Formular Überschrift'),
            'text_below_form' => $this->getSettingValue('Padento Formular Hinweis unterhalb Formularfelder'),
            'dsgvo_terms'     => $this->getSettingValue('Padento Formular DSGVO Text'),
            'button_text'     => $this->getSettingValue('Padento Formular Button Text'),
            'footer_text'     => $this->getSettingValue('Padento Formular Footer Text'),
        ];
    }

}
