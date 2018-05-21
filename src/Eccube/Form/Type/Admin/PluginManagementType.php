<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type\Admin;

use Eccube\Annotation\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class PluginManagementType extends AbstractType
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $plugin_id = $options['plugin_id'];

        $builder
            ->add('plugin_id', HiddenType::class, [
                'data' => $plugin_id,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('plugin_archive', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'ファイルを選択してください。']),
                    new Assert\File([
                        'mimeTypes' => ['application/zip', 'application/x-tar', 'application/x-gzip'],
                        'mimeTypesMessage' => 'zipファイル、tarファイル、tar.gzファイルのいずれかをアップロードしてください。',
                    ]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'plugin_management';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['plugin_id']);
    }
}
