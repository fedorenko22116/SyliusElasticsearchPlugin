<?php

declare(strict_types=1);

namespace BitBag\SyliusElasticsearchPlugin\Controller\Action\Shop;

use BitBag\SyliusElasticsearchPlugin\Controller\RequestDataHandler\DataHandlerInterface;
use BitBag\SyliusElasticsearchPlugin\Finder\ShopProductsFinderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;

abstract class AbstractSearchAction
{
    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected DataHandlerInterface $dataHandler,
        protected ShopProductsFinderInterface $finder,
        protected Environment $twig
    ) {
    }

    protected function clearInvalidEntries(FormInterface $form, array $requestData): array
    {
        foreach ($form->getErrors(true) as $error) {
            $errorOrigin = $error->getOrigin();
            $path = ($errorOrigin->getParent()->getPropertyPath() ?? '') . $errorOrigin->getPropertyPath();

            $keys = explode('][', trim($path, '[]'));

            $dataRef = &$requestData;
            foreach ($keys as $index => $key) {
                if (isset($dataRef[$key])) {
                    if ($index === count($keys) - 1) {
                        unset($dataRef[$key]);
                    } else {
                        $dataRef = &$dataRef[$key];
                    }
                }
            }
        }

        return $requestData;
    }
}
