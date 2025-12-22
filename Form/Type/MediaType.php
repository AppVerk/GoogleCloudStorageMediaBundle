<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Form\Type;

use AppVerk\GoogleCloudStorageMediaBundle\Form\DataTransformer\MediaTransformer;
use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaValidation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Override;

class MediaType extends AbstractType
{
    public function __construct(
        private readonly MediaTransformer $mediaTransformer,
        private readonly MediaValidation $mediaValidation
    ) {
    }

    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->mediaTransformer);
    }

    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['group'] = $options['group'];
        $view->vars['allowed_mime_types'] = $this->mediaValidation->getAllowedMimeTypes($options['group']);
        $view->vars['max_size'] = $this->mediaValidation->getMaxSize($options['group']);
    }

    #[Override]
    public function getParent(): string
    {
        return HiddenType::class;
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('group', null);
    }
}
