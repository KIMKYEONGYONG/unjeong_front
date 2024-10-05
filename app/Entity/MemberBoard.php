<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\HasTimestamps;
use App\Enum\Board\BoardType;
use App\Enum\Board\BoardStatus;
use App\Helper\ReadFileHelper;
use App\Interfaces\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table('member_board')]
#[HasLifecycleCallbacks]
class MemberBoard implements EntityInterface
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name:"board_type", enumType: BoardType::class)] // 게시글 type : ex) : notice : 공지사항
    private BoardType $boardType;

    #[ManyToOne(targetEntity: AdminMember::class)]
    #[JoinColumn(name: 'admin_id',referencedColumnName: 'id')]
    private ?AdminMember $adminMember;

    #[Column(length: 255)] // 제목
    private string $subject;

    #[Column]
    private string $content = '';


    #[Column(enumType: BoardStatus::class)]
    private BoardStatus $status;

    public function getId(): int
    {
        return $this->id;
    }

    public function getBoardType(): BoardType
    {
        return $this->boardType;
    }

    public function getAdminMember(): ?AdminMember
    {
        return $this->adminMember;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function getMediaName(): ?string
    {
        return $this->mediaName;
    }



    public function getStatus(): BoardStatus
    {
        return $this->status;
    }

    public function fileDir() : string
    {
        if(empty($this->getFile())){
            return '';
        }
        $file = ReadFileHelper::getFilePath($this->getFile());
        return  $file['dir'] ?? '';
    }

    public function orgFileName() : string
    {
        if(empty($this->getFile())){
            return '';
        }
        $file = ReadFileHelper::getFilePath($this->getFile());
        return   $file['orgName'] ?? '';
    }


    public function uploadFileName() :string
    {
        if(empty($this->getFile())){
            return '';
        }
        $file = ReadFileHelper::getFilePath($this->getFile());
        return $file['fileName'] ?? '';
    }

}