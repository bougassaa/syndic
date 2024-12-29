<?php

namespace App\Controller;

use App\Entity\Cotisation;
use App\Entity\Depense;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait SavePreuves
{
    private function handlePreuves(FormInterface $form, Cotisation|Depense $cotisation, string $folder): void
    {
        if ($form->has('existingPreuves')) {
            $existingPreuves = $form->get('existingPreuves')->getData();
            $existingPreuves = json_decode($existingPreuves, true);
            $cotisation->setPreuves($existingPreuves);
        }

        /** @var UploadedFile[] $preuves */
        $preuves = $form->get('preuves')->getData();

        if (!empty($preuves)) {
            foreach ($preuves as $file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter( $folder . '_preuves'), $filename);
                $cotisation->addPreuve($filename);
            }
        }
    }
}