<?php

namespace App\Form\Type;

use App\Entity\CatalogueItem;
use App\Service\CurrencyConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class CatalogueItemType extends AbstractType
{
    private CurrencyConverter $currencyConverter;

    public function __construct(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'convertToPence']);
        $builder->add('name', TextType::class, []);
        $builder->add('cost', TextType::class, []);
    }

    public function convertToPence(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Convert the cost string to pence integer before submitting the form
        if ($data !== null && is_string($data)) {
            $penceValue = $this->currencyConverter->stringToPence($data);
            $event->setData($penceValue);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CatalogueItem::class,
        ]);
    }
}