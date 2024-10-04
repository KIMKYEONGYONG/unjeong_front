<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\EntityMapper;
use App\DataObjects\UploadedFileData;
use App\Entity\Board;
use App\Enum\Board\BoardStatus;
use App\Enum\Board\BoardType;
use App\Helper\PaginatorHelper;
use App\Helper\QueryConditionHelper;
use App\Helper\ReadFileHelper;
use App\Helper\UploadFileHelper;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionException;

class BoardService
{
    use EntityServiceTrait;

    public function __construct(
        private readonly DefaultEntityManager $entityManager,
        private readonly EntityMapper $entityMapper,
        private readonly PaginatorHelper $paginator,
        private readonly QueryConditionHelper $conditionHelper,
        private readonly UploadFileHelper        $fileHelper,
    ){}

    /**
     * @throws NotSupported
     */
    public function list(Request $request, BoardType $boardType, string $platform = 'master') : PaginatorHelper
    {
        $query = $this->entityManager
            ->getRepository(Board::class)
            ->createQueryBuilder('B')
            ->where('B.boardType = :boardType')
            ->setParameter('boardType', $boardType)
            ->orderBy('B.createdAt','desc');
        if($platform !== 'master'){
            $query
                ->andWhere('B.status = :status')
                ->setParameter('status', BoardStatus::Active)
                ;

        }
        $query = $this->conditionHelper->dateCondition($query, 'startDate', 'endDate','B.createdAt',$request);
        $query = $this->conditionHelper->keywordCondition($query,['B.subject'],$request);
        return $this->paginator->paginate($query,$request);
    }

    /**
     * @throws ReflectionException| ORMException
     */
    public function register(array $data): void
    {
        /** @var Board $board */
        $board = $this->entityMapper->mapper(Board::class,$data);
        $this->persistFlush($board);

        if(!empty($data['file'])){
            $data = $this->tempFileUpload($data, $board);
        }
        if(!empty($data['thumbnailFile'])){
            $data = $this->thumbnailFileUpload($data, $board);
        }

        $this->entityMapper->mapper($board,$data);
        $this->persistFlush($board);

    }

    /**
     * @throws ReflectionException| ORMException
     */
    public function update(Board $board, array $data): void
    {
        $path = BOARD_DIR.'/' . $board->getId().'/';
        if(!empty($data['file'])) {
            if(!empty($board->getFile())){
                $file = ReadFileHelper::getFilePath($board->getFile())['fileName'];
                $this->fileHelper->removeFile($path,$file);
            }
            $data = $this->tempFileUpload($data, $board);
        }

        if(!empty($data['thumbnailFile'])){
            if(!empty($board->getThumbnail())){
                $file = ReadFileHelper::getFilePath($board->getThumbnail())['fileName'];
                $this->fileHelper->removeFile($path,$file);
            }
            $data = $this->thumbnailFileUpload($data, $board);
        }



        $this->entityMapper->mapper($board,$data);
        $this->persistFlush($board);
    }

    /**
     * @throws ORMException
     */
    public function delete(Board $board): void
    {
        $this->fileHelper->removeDir(BOARD_DIR.'/'.$board->getId());
        $this->removeFlush($board);
    }


    public function getById(int $id): ?Board
    {
        try{
            return $this->entityManager->find(Board::class, $id);
        }catch (ORMException ){}
        return null;
    }

    /**
     * @throws NotSupported
     */
    public function listJson(Request $request, BoardType $boardType) : array
    {
        $boards= $this->list($request, $boardType,'api');
        $lists = $this->paginator->toArray($boards);
        $list['items'] = [];
        /* @var Board $item */
        foreach($lists['items'] as $key => $item){
            $admin = $item->getAdminMember();
            $list['items'][$key]['id'] = $item->getId();
            $list['items'][$key]['subject'] = $item->getSubject();
            $list['items'][$key]['bdName'] = $admin?->getName() ?? '';
            $list['items'][$key]['thumbnail'] = empty($item->getThumbnail()) ? '' : FILE_DOMAIN. ReadFileHelper::getFilePath($item->getThumbnail())['dir'].'/'. ReadFileHelper::getFilePath($item->getThumbnail())['fileName'];
            $list['items'][$key]['linkUrl'] = $item->getLinkUrl() ?? '';
            $list['items'][$key]['bdName'] = $admin?->getName() ?? '';
            $list['items'][$key]['mediaName'] = $item->getMediaName() ?? '';
            $list['items'][$key]['createdAt'] = $item->getCreatedAt()->format('Y-m-d');
            $list['items'][$key]['updatedAt'] = $item->getUpdatedAt()->format('Y-m-d');
            $list['items'][$key]['isFile'] = !empty($item->getFile());

        }
        $lists['items'] = $list['items'];

        return $lists;
    }

    public function detailJson(Board $board) : array
    {
        $admin = $board->getAdminMember();
        return [
            'id' => $board->getId(),
            'subject' => $board->getSubject(),
            'bdName' => $admin?->getName() ?? '',
            'contents' => $board->getContent(),
            'createdAt' => $board->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $board->getUpdatedAt()->format('Y-m-d H:i:s'),
            'fileUrl' => FILE_DOMAIN.$board->fileDir().'/'.$board->uploadFileName(),
            'fileName' => $board->orgFileName(),
            'thumbnail' => empty($board->getThumbnail()) ? '' : FILE_DOMAIN. ReadFileHelper::getFilePath($board->getThumbnail())['dir'].'/'. ReadFileHelper::getFilePath($board->getThumbnail())['fileName'],
            'linkUrl' => $board->getLinkUrl()?? '',

        ];
    }


    private function tempFileUpload(array $data, Board $board) : array
    {
        $bannerFile = $this->fileUpload($data['file'], (string)$board->getId());
        $fileName = $bannerFile->uploadedFileName;
        $orgName = $bannerFile->clientName;
        $filePath = ['dir' => BOARD_DIR. '/'.$board->getId(), 'fileName' => $fileName, 'orgName' => $orgName  ];
        $data['file'] = ReadFileHelper::getFileCode($filePath);
        return $data;
    }

    private function thumbnailFileUpload(array $data, Board $board) : array
    {
        $bannerFile = $this->fileUpload($data['thumbnailFile'], (string)$board->getId());
        $fileName = $bannerFile->uploadedFileName;
        $orgName = $bannerFile->clientName;
        $filePath = ['dir' => BOARD_DIR. '/'.$board->getId(), 'fileName' => $fileName, 'orgName' => $orgName  ];
        $data['thumbnail'] = ReadFileHelper::getFileCode($filePath);
        return $data;
    }

    private function fileUpload(UploadedFileInterface $file, string $path): UploadedFileData
    {
        return $this->fileHelper->moveUploadedFile(
            BOARD_DIR.'/'.$path,
            $file,
        );
    }



}