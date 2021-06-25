/****** Script for SelectTopNRows command from SSMS  ******/
select * from Images
select * from Tags
select * from TaggedImages

select Images.FullFilePath, Images.FileName, Images.width, Images.height, Tags.Tag
from Images
join TaggedImages on Images.ImageID = TaggedImages.ImageID
join Tags on TaggedImages.TagID = Tags.TagID

--truncate table TaggedImages
--truncate table Tags
--truncate table Images