<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form for booking in room
 */
class BookingInRoomType extends BookingType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->remove('room');
    }
}
