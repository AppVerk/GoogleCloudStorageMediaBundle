# GoogleCloudStorageMediaBundle

Symfony Media Bundle. The bundle allows for easy file uploads. The bundle requires the [dropzone.js](http://www.dropzonejs.com/) script to work.

## Configure

Require the bundle with composer:

    $ composer require app-verk/google-cloud-storage-media-bundle

Enable the bundle in the kernel:

    // config/bundles.php

    return [
        // ...
        AppVerk\GoogleCloudStorageMediaBundle\GoogleCloudStorageMediaBundle::class => ['all' => true],
    ];

Create your Media class:
    
    <?php
    
    namespace App\Entity;
    
    use AppVerk\GoogleCloudStorageMediaBundle\Entity\Media as BaseMedia;
    use Doctrine\ORM\Mapping as ORM;
    
    #[ORM\Entity]
    class Media extends BaseMedia
    {
    
    }
    
Add to config/packages/twig.yaml:

    twig:
        form_themes:
            - '@GoogleCloudStorageMedia/form/fields.html.twig'
                
Add to config/packages/google_cloud_storage_media.yaml:

    google_cloud_storage_media:
        namer: "AppVerk\\GoogleCloudStorageMediaBundle\\Namer\\DefaultNamer"
        filesystem: "default.storage"
        filesystem_url_retriever: 'AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever\LocalObjectUrlRetriever'
        entities:
            media_class: App\Entity\Media
        gcs:
            project_id: 123
            bucket_id: my_bucket
            key_file_path: "default"
        allowed_mime_types: ["image/jpeg", "image/jpg", "image/png", "image/gif", "application/pdf"]
        
Add to config/routes.yaml:

    media:
        resource: '@GoogleCloudStorageMediaBundle/Controller/'
        type: attribute
                
Add these libs into your admin panel:

    <!--css -->
    <link rel="stylesheet" href="{{ asset('bundles/googlecloudstoragemedia/css/dropzone.min.css') }}" />
    
    <!-- js -->
    <script src="{{ asset('bundles/googlecloudstoragemedia/js/dropzone.min.js') }}"></script>

Update your database schema:

    $ php bin/console doctrine:schema:update --force
    
## Media Form Type

    <?php
    
    namespace App\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use AppVerk\GoogleCloudStorageMediaBundle\Form\Type\MediaType;
    use Symfony\Component\Form\FormBuilderInterface;
    
    class PostType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('image', MediaType::class)
            ;
        }
    }
    
## Twig helper

Render a media:

    <img src="{{ post.media|media }}" />

## Group of validation

Bundle allow to validation every single used MediaType in different way. For example you want to allow only PDF files: 

You need to add group into config/packages/google_cloud_storage_media.yaml:

    google_cloud_storage_media:
        entities:
            media_class: App\Entity\Media
        allowed_mime_types: ["image/png", "image/gif"]
        max_file_size: 15000000
        groups:
            lorem:
                allowed_mime_types: ["application/pdf"]
                max_file_size: 560000

Set group in MediaType:
    
    $builder->add('image', MediaType::class, [
        'group' => 'lorem'
    ]);

## Using new StorageService and flysystem layer

In order to use the new `AppVerk\GoogleCloudStorageMediaBundle\Service\v2\StorageService` you have to:

Configure flysystem correctly (example):

**config/packages/flysystem.yaml**
```
flysystem:
    storages:
        default.storage:
            adapter: 'gcloud'
            public: true
            options:
                client: 'gcloud_client_service'
                bucket: 'some-bucket-name'
```

**config/services/storage.yaml**
```
parameters:

services:
    _defaults:
        autowire: true
        autoconfigure: true

    gcloud_client_service:
        class: Google\Cloud\Storage\StorageClient
        arguments:
            - projectId: '%google_cloud_storage_media.gcs.project_id%'
              keyFilePath: '%google_cloud_storage_media.gcs.key_file_path%'
```

Then, configure media bundle accordingly
**config/packages/google_cloud_storage_media.yaml**
```
google_cloud_storage_media:
    ...
    gcs:
        project_id: your-project-id
        bucket_id: your-bucket-id
        key_file_path: path-to-keyfile
    namer: "AppVerk\\GoogleCloudStorageMediaBundle\\Namer\\NamerInterface" # default
    filesystem: "default.storage" # use your storage name from flysystem here
    filesystem_url_retriever: 'AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever\GoogleObjectUrlRetriever' # configure url retriever for proper path storing
```


## License

The bundle is released under the [MIT License](LICENSE).
