<?php

namespace App\Controller\Admin;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Poster;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PosterCrudController extends AbstractCrudController
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public static function getEntityFqcn(): string
    {
        return Poster::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('album'),
            TextField::new('artist'),
            IntegerField::new('quantity'),
            DateTimeField::new('added_at')->setFormat('Y-MM-dd HH:mm:ss'),
            DateTimeField::new('updated_at')->setFormat('Y-MM-dd HH:mm:ss'),
            ImageField::new('posterFile')
            ->setLabel('posterFile')
            ->setBasePath('/uploads/posters')
            ->onlyOnIndex(),
            // Add the fileupload field
            TextField::new('posterFile', 'Upload File')
                ->setFormType(FileType::class)
                ->setFormTypeOption('mapped', false)
                ->onlyOnForms(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleFileUpload($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleFileUpload($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleFileUpload(Poster $post): void
    {
        $uploadedFile = $this->getContext()->getRequest()->files->get('Poster')['posterFile'];
        if ($uploadedFile instanceof UploadedFile) {
            $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
            $symfonyUploadsDir = $this->parameterBag->get('uploads_directory');
            $angularAssetsDir = $this->parameterBag->get('angular_assets_directory');

            // Move file to Symfony uploads directory
            $uploadedFile->move($symfonyUploadsDir, $fileName);

            // Ensure the Angular assets directory exists
            $filesystem = new Filesystem();
            if (!$filesystem->exists($angularAssetsDir)) {
                $filesystem->mkdir($angularAssetsDir, 0777);
            }

            // Copy file to Angular assets directory
            $filesystem->copy($symfonyUploadsDir . '/' . $fileName, $angularAssetsDir . '/' . $fileName);

            $post->setPosterFile($fileName);
        }
    }
}
