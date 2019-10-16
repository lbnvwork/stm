<?php
/**
 * Created by PhpStorm.
 * User: Maksim
 * Date: 21.04.2019
 * Time: 14:38
 */

namespace Office\Helper;

use App\Helper\UrlHelper;
use Zend\View\Helper\AbstractHelper;

/**
 * Class TemplateHelper
 * Хелпер проверяет, является ли текущий маршрут формой для создания сущности
 *
 * @package Office\Helper\
 */
class TemplateHelper extends AbstractHelper
{
    private $urlHelper;


    /**
     * TemplateHelper constructor.
     *
     * @param UrlHelper $urlHelper
     */
    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param string $currUrl
     *
     * @return bool
     */
    public function __invoke(string $currUrl): bool
    {
        $newArr = [
            //$this->urlHelper->generate('office.firmware.new'),
            $this->urlHelper->generate('office.company.new'),
            $this->urlHelper->generate('office.kkt.new'),
            $this->urlHelper->generate('admin.user.new'),
            //$this->urlHelper->generate('office.db.new'),
        ];

        return in_array($currUrl, $newArr) ? true : false;
    }
}
