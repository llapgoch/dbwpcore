<?php

namespace DaveBaker\Core\Block\Components;
/**
 * Class ProgressBar
 * @package DaveBaker\Core\Block\Components
 */
class ProgressBar
    extends \DaveBaker\Core\Block\Html\Base
{
    const PERCENTAGE_DATA_KEY = 'percentage';

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier('progress-bar');
        parent::_construct();
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('components/progress-bar.phtml');
    }

    /**
     * @param $percentage
     * @return $this
     */
    public function setPercentage($percentage)
    {
        $this->setData(self::PERCENTAGE_DATA_KEY, (float) $percentage);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getPercentage()
    {
        return $this->getData(self::PERCENTAGE_DATA_KEY);
    }

    /**
     * @return float|string
     */
    public function getPercentageText()
    {
        return ((float) $this->getData(self::PERCENTAGE_DATA_KEY)) . "%";
    }
}